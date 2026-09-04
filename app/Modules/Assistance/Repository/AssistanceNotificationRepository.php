<?php

declare(strict_types=1);

namespace App\Modules\Assistance\Repository;

use App\Core\BaseRepository;

final class AssistanceNotificationRepository extends BaseRepository
{
    protected string $table = 'assistance_notifications';
    protected bool $softDeletes = false;

    public function preference(int $requestId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM assistance_notification_preferences WHERE assistance_request_id=:request_id LIMIT 1');
        $stmt->execute(['request_id' => $requestId]);
        return $stmt->fetch() ?: ['email_enabled'=>1,'sms_enabled'=>1,'whatsapp_enabled'=>1];
    }

    public function savePreference(int $requestId, ?int $userId, array $values): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO assistance_notification_preferences
                (assistance_request_id,user_id,email_enabled,sms_enabled,whatsapp_enabled)
             VALUES (:request_id,:user_id,:email,:sms,:whatsapp)
             ON DUPLICATE KEY UPDATE
                user_id=VALUES(user_id), email_enabled=VALUES(email_enabled), sms_enabled=VALUES(sms_enabled), whatsapp_enabled=VALUES(whatsapp_enabled)'
        );
        return $stmt->execute([
            'request_id' => $requestId,
            'user_id' => $userId,
            'email' => !empty($values['email_enabled']) ? 1 : 0,
            'sms' => !empty($values['sms_enabled']) ? 1 : 0,
            'whatsapp' => !empty($values['whatsapp_enabled']) ? 1 : 0,
        ]);
    }

    public function template(string $event, string $channel): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM assistance_notification_templates WHERE event=:event AND channel=:channel AND enabled=1 LIMIT 1');
        $stmt->execute(['event' => $event, 'channel' => $channel]);
        return $stmt->fetch() ?: null;
    }

    public function notificationExists(int $requestId, string $channel, string $event, ?string $sourceType, ?int $sourceId): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM assistance_notifications WHERE assistance_request_id=:request_id AND channel=:channel AND event=:event AND source_type <=> :source_type AND source_id <=> :source_id');
        $stmt->execute(['request_id'=>$requestId,'channel'=>$channel,'event'=>$event,'source_type'=>$sourceType,'source_id'=>$sourceId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function createNotification(array $data): int
    {
        $columns = array_keys($data);
        $sql = 'INSERT INTO assistance_notifications (' . implode(',', $columns) . ') VALUES (' . implode(',', array_map(fn($c) => ':' . $c, $columns)) . ')';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function updateNotification(int $id, array $data): bool
    {
        $assign = implode(',', array_map(fn($c) => $c . '=:' . $c, array_keys($data)));
        $stmt = $this->db->prepare('UPDATE assistance_notifications SET ' . $assign . ' WHERE id=:id');
        return $stmt->execute([...$data, 'id' => $id]);
    }

    public function createAttempt(int $notificationId, int $attempt, string $status, ?string $messageId, ?string $error): int
    {
        $stmt = $this->db->prepare('INSERT INTO assistance_notification_attempts (notification_id,attempt_number,status,provider_message_id,error_message) VALUES (:notification_id,:attempt_number,:status,:message_id,:error)');
        $stmt->execute(['notification_id'=>$notificationId,'attempt_number'=>$attempt,'status'=>$status,'message_id'=>$messageId,'error'=>$error]);
        return (int)$this->db->lastInsertId();
    }

    public function retryable(int $maxAttempts = 3, int $limit = 50): array
    {
        $maxAttempts = max(1, min($maxAttempts, 10));
        $limit = max(1, min($limit, 200));
        $sql = "SELECT n.*, ar.name, ar.phone, ar.email, ar.request_number
                FROM assistance_notifications n
                JOIN assistance_requests ar ON ar.id=n.assistance_request_id
                WHERE n.status='failed'
                  AND n.attempt_count < :max_attempts
                  AND (n.next_attempt_at IS NULL OR n.next_attempt_at <= NOW())
                ORDER BY n.created_at ASC
                LIMIT {$limit}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['max_attempts' => $maxAttempts]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT n.*, ar.name, ar.phone, ar.email, ar.request_number FROM assistance_notifications n JOIN assistance_requests ar ON ar.id=n.assistance_request_id WHERE n.id=:id LIMIT 1');
        $stmt->execute(['id'=>$id]);
        return $stmt->fetch() ?: null;
    }

    public function recentForRequest(int $requestId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM assistance_notifications WHERE assistance_request_id=:request_id ORDER BY created_at DESC,id DESC LIMIT 100');
        $stmt->execute(['request_id'=>$requestId]);
        return $stmt->fetchAll();
    }
}
