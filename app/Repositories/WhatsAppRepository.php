<?php

declare(strict_types=1);

namespace AlbaTech\Repositories;

use AlbaTech\Core\Database;
use AlbaTech\Core\WhatsAppPolicy;
use PDO;

/**
 * Storage for WhatsApp messages, contacts, queued webhook events, opt-outs and consents.
 * Time comparisons run in SQL (NOW()) so PHP and database time zones cannot disagree.
 */
final class WhatsAppRepository
{
    private const MESSAGE_COLUMNS = ['direction', 'origin', 'admin_user_id', 'wa_number', 'contact_name', 'message_type', 'body', 'wa_message_id', 'status', 'error_code', 'error_message', 'payload', 'request_json', 'attempts', 'next_attempt_at', 'media_id', 'media_mime'];

    private function db(): PDO
    {
        return Database::connection();
    }

    // ---- messages -------------------------------------------------------------------------

    /**
     * Insert a message. Returns the new id, or null when Meta's message id was already
     * stored (a webhook retry), so callers can skip re-processing.
     * @param array<string, mixed> $row
     */
    public function recordMessage(array $row): ?int
    {
        $row = array_intersect_key($row, array_flip(self::MESSAGE_COLUMNS));
        if (($row['wa_message_id'] ?? '') === '') {
            $row['wa_message_id'] = null;
        }
        $columns = array_keys($row);
        $sql = 'INSERT IGNORE INTO whatsapp_messages (' . implode(',', $columns) . ') VALUES (:' . implode(',:', $columns) . ')';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($row);
        return $stmt->rowCount() === 1 ? (int) $this->db()->lastInsertId() : null;
    }

    /** @param array<string, mixed> $fields */
    public function updateMessage(int $id, array $fields): void
    {
        $fields = array_intersect_key($fields, array_flip(array_merge(self::MESSAGE_COLUMNS, ['media_path'])));
        if ($fields === []) {
            return;
        }
        $sets = implode(', ', array_map(static fn (string $c): string => "{$c} = :{$c}", array_keys($fields)));
        $fields['id'] = $id;
        $this->db()->prepare("UPDATE whatsapp_messages SET {$sets} WHERE id = :id")->execute($fields);
    }

    public function scheduleRetry(int $id, int $attempts, int $delaySeconds): void
    {
        $this->db()->prepare('UPDATE whatsapp_messages SET status = \'queued\', attempts = :a, next_attempt_at = NOW() + INTERVAL :d SECOND WHERE id = :id')
            ->execute(['a' => $attempts, 'd' => $delaySeconds, 'id' => $id]);
    }

    /** @return list<array<string, mixed>> */
    public function dueRetries(int $limit = 20): array
    {
        $stmt = $this->db()->prepare('SELECT * FROM whatsapp_messages WHERE status = \'queued\' AND next_attempt_at <= NOW() ORDER BY next_attempt_at ASC LIMIT :lim');
        $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return array<string, mixed>|null */
    public function message(int $id): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM whatsapp_messages WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    /** Apply a delivery status from Meta, never moving backwards (read -> delivered). */
    public function markStatus(string $waMessageId, string $status, ?string $errorCode = null, ?string $errorMessage = null): void
    {
        if ($waMessageId === '' || $status === '') {
            return;
        }
        $stmt = $this->db()->prepare('SELECT id, status FROM whatsapp_messages WHERE wa_message_id = :w AND direction = \'out\' LIMIT 1');
        $stmt->execute(['w' => $waMessageId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false || !WhatsAppPolicy::statusMayReplace((string) $row['status'], $status)) {
            return;
        }
        $this->db()->prepare('UPDATE whatsapp_messages SET status = :s, error_code = COALESCE(:c, error_code), error_message = COALESCE(:m, error_message) WHERE id = :id')
            ->execute(['s' => $status, 'c' => $errorCode, 'm' => $errorMessage !== null ? mb_substr($errorMessage, 0, 500) : null, 'id' => $row['id']]);
    }

    /** Newest $limit messages for a number, returned oldest-first for display. */
    public function thread(string $waNumber, int $limit = 200): array
    {
        $stmt = $this->db()->prepare(
            'SELECT * FROM (
                SELECT id, direction, origin, contact_name, message_type, body, wa_message_id, status, error_code, error_message, media_path, media_mime, created_at
                FROM whatsapp_messages WHERE wa_number = :n ORDER BY created_at DESC, id DESC LIMIT :lim
             ) newest ORDER BY created_at ASC, id ASC'
        );
        $stmt->bindValue('n', $waNumber);
        $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** One row per number with the latest message, contact details and window state. */
    public function conversations(int $limit = 100): array
    {
        $stmt = $this->db()->prepare(
            // Aggregate in a derived table first so the outer query has no GROUP BY:
            // works under ONLY_FULL_GROUP_BY (the production MySQL/MariaDB default).
            'SELECT agg.wa_number,
                    agg.last_at,
                    agg.total,
                    (SELECT x.body FROM whatsapp_messages x WHERE x.wa_number = agg.wa_number ORDER BY x.created_at DESC, x.id DESC LIMIT 1) AS last_body,
                    (SELECT x.direction FROM whatsapp_messages x WHERE x.wa_number = agg.wa_number ORDER BY x.created_at DESC, x.id DESC LIMIT 1) AS last_direction,
                    COALESCE(NULLIF(c.contact_name, \'\'), (SELECT x.contact_name FROM whatsapp_messages x WHERE x.wa_number = agg.wa_number AND x.contact_name <> \'\' ORDER BY x.created_at DESC LIMIT 1)) AS contact_name,
                    c.topic, c.source_page,
                    (c.last_inbound_at IS NOT NULL AND c.last_inbound_at >= NOW() - INTERVAL 24 HOUR) AS window_open,
                    (o.wa_number IS NOT NULL) AS opted_out
             FROM (SELECT wa_number, MAX(created_at) AS last_at, COUNT(*) AS total FROM whatsapp_messages GROUP BY wa_number) agg
             LEFT JOIN whatsapp_contacts c ON c.wa_number = agg.wa_number
             LEFT JOIN whatsapp_optouts o ON o.wa_number = agg.wa_number
             ORDER BY agg.last_at DESC
             LIMIT :lim'
        );
        $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contactNameFor(string $waNumber): string
    {
        $contact = $this->contact($waNumber);
        if ($contact !== null && $contact['contact_name'] !== '') {
            return (string) $contact['contact_name'];
        }
        $stmt = $this->db()->prepare('SELECT contact_name FROM whatsapp_messages WHERE wa_number = :n AND contact_name <> \'\' ORDER BY created_at DESC LIMIT 1');
        $stmt->execute(['n' => $waNumber]);
        return (string) ($stmt->fetchColumn() ?: '');
    }

    /** Minutes since the last automated (bot) message to this number; PHP_INT_MAX if none. */
    public function minutesSinceBotMessage(string $waNumber): int
    {
        $stmt = $this->db()->prepare('SELECT TIMESTAMPDIFF(MINUTE, MAX(created_at), NOW()) FROM whatsapp_messages WHERE wa_number = :n AND direction = \'out\' AND origin = \'bot\'');
        $stmt->execute(['n' => $waNumber]);
        $value = $stmt->fetchColumn();
        return $value === null || $value === false ? PHP_INT_MAX : (int) $value;
    }

    /** Number of inbound messages from this number in the last $minutes. */
    public function inboundWithinMinutes(string $waNumber, int $minutes): int
    {
        $stmt = $this->db()->prepare('SELECT COUNT(*) FROM whatsapp_messages WHERE wa_number = :n AND direction = \'in\' AND created_at >= NOW() - INTERVAL :m MINUTE');
        $stmt->bindValue('n', $waNumber);
        $stmt->bindValue('m', $minutes, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    // ---- contacts -------------------------------------------------------------------------

    /** @return array<string, mixed>|null includes computed window_open, window_closes_at and bot_paused */
    public function contact(string $waNumber): ?array
    {
        $stmt = $this->db()->prepare(
            'SELECT c.*,
                    (c.last_inbound_at IS NOT NULL AND c.last_inbound_at >= NOW() - INTERVAL 24 HOUR) AS window_open,
                    c.last_inbound_at + INTERVAL 24 HOUR AS window_closes_at,
                    (c.bot_paused_until IS NOT NULL AND c.bot_paused_until > NOW()) AS bot_paused,
                    (c.menu_sent_at IS NOT NULL AND c.menu_sent_at >= NOW() - INTERVAL 24 HOUR) AS menu_recent
             FROM whatsapp_contacts c WHERE c.wa_number = :n'
        );
        $stmt->execute(['n' => $waNumber]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    public function touchInbound(string $waNumber, string $contactName): void
    {
        $this->db()->prepare(
            'INSERT INTO whatsapp_contacts (wa_number, contact_name, last_inbound_at) VALUES (:n, :name, NOW())
             ON DUPLICATE KEY UPDATE last_inbound_at = NOW(), contact_name = IF(VALUES(contact_name) <> \'\', VALUES(contact_name), contact_name)'
        )->execute(['n' => $waNumber, 'name' => mb_substr($contactName, 0, 150)]);
    }

    public function ensureContact(string $waNumber, string $contactName = ''): void
    {
        $this->db()->prepare('INSERT IGNORE INTO whatsapp_contacts (wa_number, contact_name) VALUES (:n, :name)')
            ->execute(['n' => $waNumber, 'name' => mb_substr($contactName, 0, 150)]);
    }

    public function setTopic(string $waNumber, string $topic): void
    {
        $this->db()->prepare('UPDATE whatsapp_contacts SET topic = :t WHERE wa_number = :n')->execute(['t' => $topic, 'n' => $waNumber]);
    }

    public function setSourcePageIfEmpty(string $waNumber, string $page): void
    {
        $this->db()->prepare('UPDATE whatsapp_contacts SET source_page = :p WHERE wa_number = :n AND (source_page IS NULL OR source_page = \'\')')
            ->execute(['p' => $page, 'n' => $waNumber]);
    }

    public function pauseBot(string $waNumber, int $hours): void
    {
        $this->ensureContact($waNumber);
        $this->db()->prepare('UPDATE whatsapp_contacts SET bot_paused_until = NOW() + INTERVAL :h HOUR WHERE wa_number = :n')
            ->execute(['h' => $hours, 'n' => $waNumber]);
    }

    public function resumeBot(string $waNumber): void
    {
        $this->db()->prepare('UPDATE whatsapp_contacts SET bot_paused_until = NULL WHERE wa_number = :n')->execute(['n' => $waNumber]);
    }

    public function markMenuSent(string $waNumber): void
    {
        $this->db()->prepare('UPDATE whatsapp_contacts SET menu_sent_at = NOW() WHERE wa_number = :n')->execute(['n' => $waNumber]);
    }

    // ---- opt-outs and consent -------------------------------------------------------------

    public function isOptedOut(string $waNumber): bool
    {
        $stmt = $this->db()->prepare('SELECT 1 FROM whatsapp_optouts WHERE wa_number = :n LIMIT 1');
        $stmt->execute(['n' => $waNumber]);
        return $stmt->fetchColumn() !== false;
    }

    public function optOut(string $waNumber): void
    {
        $this->db()->prepare('INSERT IGNORE INTO whatsapp_optouts (wa_number) VALUES (:n)')->execute(['n' => $waNumber]);
    }

    public function optIn(string $waNumber): void
    {
        $this->db()->prepare('DELETE FROM whatsapp_optouts WHERE wa_number = :n')->execute(['n' => $waNumber]);
    }

    public function optOuts(): array
    {
        return $this->db()->query('SELECT wa_number, created_at FROM whatsapp_optouts ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function recordConsent(string $waNumber, string $source, string $consentText, ?string $reference, ?string $page): void
    {
        $this->db()->prepare('INSERT INTO whatsapp_consents (wa_number, source, reference, page, consent_text) VALUES (:n, :s, :r, :p, :t)')
            ->execute(['n' => $waNumber, 's' => $source, 'r' => $reference, 'p' => $page, 't' => mb_substr($consentText, 0, 500)]);
    }

    /** @return array<string, mixed>|null */
    public function latestConsent(string $waNumber): ?array
    {
        $stmt = $this->db()->prepare('SELECT source, reference, page, consent_text, created_at FROM whatsapp_consents WHERE wa_number = :n ORDER BY created_at DESC LIMIT 1');
        $stmt->execute(['n' => $waNumber]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    // ---- webhook events -------------------------------------------------------------------

    public function storeEvent(string $payload): int
    {
        $this->db()->prepare('INSERT INTO whatsapp_events (payload) VALUES (:p)')->execute(['p' => $payload]);
        return (int) $this->db()->lastInsertId();
    }

    /** Pending events older than a minute (fresh ones are being processed inline) and failed ones to retry. */
    public function eventsToProcess(int $limit = 50): array
    {
        $stmt = $this->db()->prepare(
            'SELECT id, payload, attempts FROM whatsapp_events
             WHERE (status = \'pending\' AND received_at <= NOW() - INTERVAL 1 MINUTE)
                OR (status = \'failed\' AND attempts < :max)
             ORDER BY id ASC LIMIT :lim'
        );
        $stmt->bindValue('max', WhatsAppPolicy::MAX_EVENT_ATTEMPTS, PDO::PARAM_INT);
        $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Atomically claim an event so the inline handler and the worker never both run it. */
    public function claimEvent(int $id): ?string
    {
        $stmt = $this->db()->prepare('UPDATE whatsapp_events SET status = \'processing\', attempts = attempts + 1 WHERE id = :id AND status IN (\'pending\', \'failed\')');
        $stmt->execute(['id' => $id]);
        if ($stmt->rowCount() !== 1) {
            return null;
        }
        $read = $this->db()->prepare('SELECT payload FROM whatsapp_events WHERE id = :id');
        $read->execute(['id' => $id]);
        $payload = $read->fetchColumn();
        return $payload === false ? null : (string) $payload;
    }

    public function finishEvent(int $id, ?string $error): void
    {
        $this->db()->prepare('UPDATE whatsapp_events SET status = :s, last_error = :e, processed_at = NOW() WHERE id = :id')
            ->execute(['s' => $error === null ? 'done' : 'failed', 'e' => $error !== null ? mb_substr($error, 0, 500) : null, 'id' => $id]);
    }

    /** Events stuck in "processing" (worker crashed) go back to failed so they are retried. */
    public function releaseStuckEvents(): void
    {
        $this->db()->exec('UPDATE whatsapp_events SET status = \'failed\', last_error = \'Processing interrupted\' WHERE status = \'processing\' AND received_at <= NOW() - INTERVAL 10 MINUTE');
    }

    /** @return list<array<string, mixed>> summaries only, no payload */
    public function recentEvents(int $limit = 8): array
    {
        $stmt = $this->db()->prepare('SELECT id, status, attempts, last_error, received_at, processed_at FROM whatsapp_events ORDER BY id DESC LIMIT :lim');
        $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ---- health and retention ------------------------------------------------------------

    /** @return array{last_event_at: ?string, queued: int, failed_7d: int, events_failed: int} */
    public function health(): array
    {
        $db = $this->db();
        return [
            'last_event_at' => ($v = $db->query('SELECT MAX(received_at) FROM whatsapp_events')->fetchColumn()) ? (string) $v : null,
            'queued' => (int) $db->query('SELECT COUNT(*) FROM whatsapp_messages WHERE status = \'queued\'')->fetchColumn(),
            'failed_7d' => (int) $db->query('SELECT COUNT(*) FROM whatsapp_messages WHERE direction = \'out\' AND status = \'failed\' AND created_at >= NOW() - INTERVAL 7 DAY')->fetchColumn(),
            'events_failed' => (int) $db->query('SELECT COUNT(*) FROM whatsapp_events WHERE status = \'failed\'')->fetchColumn(),
        ];
    }

    /**
     * Retention: clear raw payloads, delete processed events and expire downloaded media.
     * Returns the media file paths (relative to storage) the caller must delete.
     * @return array{payloads: int, events: int, media: list<string>}
     */
    public function purge(int $payloadDays, int $mediaDays): array
    {
        $db = $this->db();
        $payloads = $db->prepare('UPDATE whatsapp_messages SET payload = NULL, request_json = IF(status = \'queued\', request_json, NULL) WHERE (payload IS NOT NULL OR request_json IS NOT NULL) AND created_at < NOW() - INTERVAL :d DAY');
        $payloads->execute(['d' => max(1, $payloadDays)]);
        $events = $db->prepare('DELETE FROM whatsapp_events WHERE received_at < NOW() - INTERVAL :d DAY AND status IN (\'done\', \'failed\')');
        $events->execute(['d' => min(7, max(1, $payloadDays))]);
        $media = $db->prepare('SELECT id, media_path FROM whatsapp_messages WHERE media_path IS NOT NULL AND created_at < NOW() - INTERVAL :d DAY');
        $media->execute(['d' => max(1, $mediaDays)]);
        $paths = [];
        foreach ($media->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $paths[] = (string) $row['media_path'];
            $db->prepare('UPDATE whatsapp_messages SET media_path = NULL, body = CONCAT(COALESCE(body, \'\'), \' (file deleted)\') WHERE id = :id')->execute(['id' => $row['id']]);
        }
        return ['payloads' => $payloads->rowCount(), 'events' => $events->rowCount(), 'media' => $paths];
    }

    /**
     * Delete a whole conversation (data-deletion request). The opt-out row is kept so the
     * person is never messaged again. Returns media paths for the caller to delete.
     * @return list<string>
     */
    public function deleteConversation(string $waNumber): array
    {
        $db = $this->db();
        $stmt = $db->prepare('SELECT media_path FROM whatsapp_messages WHERE wa_number = :n AND media_path IS NOT NULL');
        $stmt->execute(['n' => $waNumber]);
        $paths = array_map('strval', $stmt->fetchAll(PDO::FETCH_COLUMN));
        foreach (['whatsapp_messages', 'whatsapp_contacts', 'whatsapp_consents'] as $table) {
            $db->prepare("DELETE FROM {$table} WHERE wa_number = :n")->execute(['n' => $waNumber]);
        }
        return $paths;
    }
}
