<?php

declare(strict_types=1);

namespace AlbaTech\Repositories;

use AlbaTech\Core\Database;
use PDO;

final class WhatsAppRepository
{
    public function record(string $direction, string $waNumber, string $contactName, string $type, ?string $body, string $waMessageId, string $status, ?string $payload): void
    {
        Database::connection()->prepare(
            'INSERT INTO whatsapp_messages (direction, wa_number, contact_name, message_type, body, wa_message_id, status, payload) VALUES (:direction, :wa_number, :contact_name, :message_type, :body, :wa_message_id, :status, :payload)'
        )->execute([
            'direction' => $direction,
            'wa_number' => $waNumber,
            'contact_name' => $contactName,
            'message_type' => $type,
            'body' => $body,
            'wa_message_id' => $waMessageId,
            'status' => $status,
            'payload' => $payload,
        ]);
    }

    public function isOptedOut(string $waNumber): bool
    {
        $stmt = Database::connection()->prepare('SELECT 1 FROM whatsapp_optouts WHERE wa_number = :n LIMIT 1');
        $stmt->execute(['n' => $waNumber]);
        return $stmt->fetchColumn() !== false;
    }

    public function optOut(string $waNumber): void
    {
        Database::connection()->prepare('INSERT IGNORE INTO whatsapp_optouts (wa_number) VALUES (:n)')
            ->execute(['n' => $waNumber]);
    }

    public function optIn(string $waNumber): void
    {
        Database::connection()->prepare('DELETE FROM whatsapp_optouts WHERE wa_number = :n')
            ->execute(['n' => $waNumber]);
    }

    /** @return array<int, array{wa_number: string, created_at: string}> */
    public function optOuts(): array
    {
        return Database::connection()->query('SELECT wa_number, created_at FROM whatsapp_optouts ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * One row per counterparty number with the latest message and contact name.
     * @return array<int, array<string, mixed>>
     */
    public function conversations(int $limit = 100): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT m.wa_number,
                    MAX(m.created_at) AS last_at,
                    (SELECT x.body FROM whatsapp_messages x WHERE x.wa_number = m.wa_number ORDER BY x.created_at DESC, x.id DESC LIMIT 1) AS last_body,
                    (SELECT x.direction FROM whatsapp_messages x WHERE x.wa_number = m.wa_number ORDER BY x.created_at DESC, x.id DESC LIMIT 1) AS last_direction,
                    (SELECT x.contact_name FROM whatsapp_messages x WHERE x.wa_number = m.wa_number AND x.contact_name <> \'\' ORDER BY x.created_at DESC, x.id DESC LIMIT 1) AS contact_name,
                    COUNT(*) AS total,
                    (o.wa_number IS NOT NULL) AS opted_out
             FROM whatsapp_messages m
             LEFT JOIN whatsapp_optouts o ON o.wa_number = m.wa_number
             GROUP BY m.wa_number, o.wa_number
             ORDER BY last_at DESC
             LIMIT :lim'
        );
        $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return array<int, array<string, mixed>> */
    public function thread(string $waNumber, int $limit = 200): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT direction, contact_name, message_type, body, wa_message_id, status, created_at
             FROM whatsapp_messages WHERE wa_number = :n ORDER BY created_at ASC, id ASC LIMIT :lim'
        );
        $stmt->bindValue('n', $waNumber);
        $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contactNameFor(string $waNumber): string
    {
        $stmt = Database::connection()->prepare(
            'SELECT contact_name FROM whatsapp_messages WHERE wa_number = :n AND contact_name <> \'\' ORDER BY created_at DESC LIMIT 1'
        );
        $stmt->execute(['n' => $waNumber]);
        return (string) ($stmt->fetchColumn() ?: '');
    }

    public function markStatus(string $waMessageId, string $status): void
    {
        if ($waMessageId === '' || $status === '') {
            return;
        }
        Database::connection()->prepare('UPDATE whatsapp_messages SET status = :s WHERE wa_message_id = :w AND direction = \'out\'')
            ->execute(['s' => $status, 'w' => $waMessageId]);
    }

    /** Last time we sent an outbound message to this number (for reply throttling). */
    public function lastOutboundAt(string $waNumber): ?string
    {
        $stmt = Database::connection()->prepare(
            'SELECT created_at FROM whatsapp_messages WHERE wa_number = :n AND direction = \'out\' ORDER BY created_at DESC LIMIT 1'
        );
        $stmt->execute(['n' => $waNumber]);
        $value = $stmt->fetchColumn();
        return $value === false ? null : (string) $value;
    }

    /** Number of inbound messages from this number since a MySQL datetime. */
    public function inboundSince(string $waNumber, string $since): int
    {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM whatsapp_messages WHERE wa_number = :n AND direction = \'in\' AND created_at >= :since'
        );
        $stmt->execute(['n' => $waNumber, 'since' => $since]);
        return (int) $stmt->fetchColumn();
    }
}
