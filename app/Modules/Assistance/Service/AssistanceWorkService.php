<?php

declare(strict_types=1);

namespace App\Modules\Assistance\Service;

use App\Core\AuditLog;
use App\Core\BaseService;
use App\Core\Config;
use App\Modules\Assistance\Repository\AssistanceRequestRepository;
use App\Modules\Assistance\Repository\AssistanceWorkRepository;
use App\Modules\Assistance\Service\AssistanceNotificationService;
use App\Modules\Growth\Service\GrowthAnalyticsService;

final class AssistanceWorkService extends BaseService
{
    public function __construct(
        private readonly AssistanceWorkRepository $work,
        private readonly AssistanceRequestRepository $requests,
        private readonly AssistanceNotificationService $notifications,
        private readonly GrowthAnalyticsService $growth
    ) {}

    public function portalTokenForRequest(array $request): string
    {
        if (!empty($request['customer_token_encrypted'])) {
            $token = $this->decryptToken((string) $request['customer_token_encrypted']);
            if ($token !== null) {
                return $token;
            }
        }

        $token = bin2hex(random_bytes(24));
        $key = hash('sha256', (string) Config::get('app.key', ''), true);
        $iv = random_bytes(12);
        $tag = '';
        $cipher = openssl_encrypt($token, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($cipher === false) {
            throw new \RuntimeException('Unable to protect customer portal token.');
        }

        $this->db()->prepare('UPDATE assistance_requests SET customer_token_hash=:hash, customer_token_encrypted=:encrypted WHERE id=:id')
            ->execute([
                'hash' => hash('sha256', $token),
                'encrypted' => base64_encode($iv . $tag . $cipher),
                'id' => (int) $request['id'],
            ]);

        AuditLog::record('assistance_portal.token_issued', 'assistance_request', (int) $request['id']);
        return $token;
    }

    public function decryptToken(string $encrypted): ?string
    {
        $key = hash('sha256', (string) Config::get('app.key', ''), true);
        $raw = base64_decode($encrypted, true);
        if ($raw === false || strlen($raw) < 28) return null;
        $plain = openssl_decrypt(substr($raw, 28), 'aes-256-gcm', $key, OPENSSL_RAW_DATA, substr($raw, 0, 12), substr($raw, 12, 16));
        return $plain === false ? null : $plain;
    }

    public function assign(int $requestId, ?int $staffId, ?string $dueAt, int $adminId): array
    {
        $item = $this->requests->findWithDetails($requestId);
        if (!$item) return ['success' => false, 'message' => 'Request not found.'];
        if ($staffId !== null && !$this->staffExists($staffId)) return ['success' => false, 'message' => 'Selected staff member is not active.'];
        $this->db()->prepare('UPDATE assistance_requests SET assigned_to=:staff, assigned_at=CASE WHEN :staff2 IS NULL THEN assigned_at ELSE NOW() END, due_at=:due, updated_at=NOW() WHERE id=:id')
            ->execute(['staff' => $staffId, 'staff2' => $staffId, 'due' => $dueAt ?: null, 'id' => $requestId]);
        AuditLog::record('assistance_work.assigned', 'assistance_request', $requestId, ['assigned_to' => $staffId, 'due_at' => $dueAt]);
        $fresh = $this->requests->findWithDetails($requestId) ?? $item;
        $this->notifications->workAssigned($fresh);
        return ['success' => true];
    }

    public function addTask(int $requestId, string $title, ?string $description, string $priority, ?int $assignee, ?string $dueAt, int $adminId): array
    {
        $title = trim($title);
        if ($title === '') return ['success' => false, 'message' => 'Task title is required.'];
        if (!in_array($priority, ['low', 'normal', 'high', 'urgent'], true)) $priority = 'normal';
        if ($assignee !== null && !$this->staffExists($assignee)) $assignee = null;
        $id = $this->work->createTask(['assistance_request_id' => $requestId, 'title' => $title, 'description' => trim((string) $description) ?: null, 'priority' => $priority, 'assigned_to' => $assignee, 'due_at' => $dueAt ?: null, 'created_by' => $adminId]);
        AuditLog::record('assistance_task.created', 'assistance_task', $id, ['request_id' => $requestId]);
        return ['success' => true, 'id' => $id];
    }

    public function updateTask(int $taskId, string $status, ?int $assignee, ?string $dueAt, int $adminId): array
    {
        $task = $this->work->task($taskId);
        if (!$task) return ['success' => false, 'message' => 'Task not found.'];
        if (!in_array($status, ['pending', 'in_progress', 'blocked', 'completed'], true)) return ['success' => false, 'message' => 'Invalid task status.'];
        if ($assignee !== null && !$this->staffExists($assignee)) return ['success' => false, 'message' => 'Selected staff member is not active.'];
        $this->work->updateTask($taskId, ['status' => $status, 'assigned_to' => $assignee, 'due_at' => $dueAt ?: null, 'completed_at' => $status === 'completed' ? date('Y-m-d H:i:s') : null]);
        if ($status === 'in_progress') {
            $rid = (int) $task['assistance_request_id'];
            $started = $this->startIfNeeded($rid);
            if ($started) { $fresh = $this->requests->findWithDetails($rid); if ($fresh) $this->notifications->workStarted($fresh); }
        }
        AuditLog::record('assistance_task.updated', 'assistance_task', $taskId, ['status' => $status]);
        return ['success' => true];
    }

    public function addUpdate(int $requestId, int $authorId, string $message, string $visibility = 'customer', string $type = 'progress'): array
    {
        $message = trim($message);
        if ($message === '') return ['success' => false, 'message' => 'Update message is required.'];
        if (!in_array($visibility, ['customer', 'internal'], true)) $visibility = 'customer';
        if (!in_array($type, ['progress', 'request', 'completed', 'note'], true)) $type = 'progress';
        $id = $this->work->addUpdate(['assistance_request_id' => $requestId, 'author_id' => $authorId, 'visibility' => $visibility, 'update_type' => $type, 'message' => $message]);
        if ($visibility === 'customer') {
            $started = $this->startIfNeeded($requestId);
            $fresh = $this->requests->findWithDetails($requestId);
            if ($fresh) {
                if ($started) $this->notifications->workStarted($fresh, $id);
                $this->notifications->progressUpdate($fresh, $id, $message);
            }
        }
        AuditLog::record('assistance_update.created', 'assistance_update', $id, ['request_id' => $requestId, 'visibility' => $visibility]);
        return ['success' => true, 'id' => $id];
    }

    public function complete(int $requestId, string $note, int $adminId): array
    {
        $item = $this->requests->findWithDetails($requestId);
        if (!$item) return ['success' => false, 'message' => 'Request not found.'];
        $note = trim($note);
        if ($note === '') return ['success' => false, 'message' => 'Add a short completion note.'];
        $this->db()->prepare("UPDATE assistance_requests SET status='completed', started_at=COALESCE(started_at,NOW()), completed_at=NOW(), completion_note=:note, updated_at=NOW() WHERE id=:id")
            ->execute(['note' => $note, 'id' => $requestId]);
        $this->requests->addHistory($requestId, $item['status'], 'completed', $adminId, $note);
        $this->work->addUpdate(['assistance_request_id' => $requestId, 'author_id' => $adminId, 'visibility' => 'customer', 'update_type' => 'completed', 'message' => $note]);
        $token = $this->reviewToken($requestId, $item);
        $fresh = $this->requests->findWithDetails($requestId) ?? $item;
        $this->notifications->workCompleted($fresh, $note, rtrim(Config::get('app.url', ''), '/') . '/review/' . rawurlencode($token));
        AuditLog::record('assistance_work.completed', 'assistance_request', $requestId);
        $this->growth->event('assistance_completed', null, !empty($item['service_id']) ? (int) $item['service_id'] : null, $requestId);
        return ['success' => true, 'review_token' => $token];
    }

    public function reviewPublicUrl(int $requestId, array $request): string
    {
        $token = $this->reviewToken($requestId, $request);
        return rtrim(Config::get('app.url', ''), '/') . '/review/' . rawurlencode($token);
    }

    public function reviewToken(int $requestId, array $request): string
    {
        $existing = $this->work->reviewByRequest($requestId);
        if ($existing) return $this->decryptToken((string) $existing['public_token_encrypted']) ?? '';

        $token = bin2hex(random_bytes(24));
        $key = hash('sha256', (string) Config::get('app.key', ''), true);
        $iv = random_bytes(12); $tag = '';
        $cipher = openssl_encrypt($token, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($cipher === false) throw new \RuntimeException('Unable to protect review token.');
        $this->work->createReview(['assistance_request_id' => $requestId, 'public_token_hash' => hash('sha256', $token), 'public_token_encrypted' => base64_encode($iv . $tag . $cipher), 'rating' => 0, 'customer_name' => $request['name'], 'status' => 'pending']);
        AuditLog::record('assistance_review.token_issued', 'assistance_request', $requestId);
        return $token;
    }

    public function submitReview(string $token, int $rating, string $comment): array
    {
        $review = $this->work->reviewByToken($token);
        if (!$review) return ['success' => false, 'message' => 'Review link not found.'];
        if ($review['request_status'] !== 'completed') return ['success' => false, 'message' => 'Reviews are available after the work is completed.'];
        if ($rating < 1 || $rating > 5) return ['success' => false, 'message' => 'Choose a rating from 1 to 5.'];
        if ($review['status'] !== 'pending') return ['success' => false, 'message' => 'This review has already been submitted.'];
        $this->work->updateReview((int) $review['id'], ['rating' => $rating, 'comment' => trim($comment) ?: null, 'status' => 'pending', 'reviewed_at' => date('Y-m-d H:i:s')]);
        $this->growth->event('review_submitted', null, !empty($review['service_id']) ? (int) $review['service_id'] : null, (int) $review['assistance_request_id'], ['rating' => $rating]);
        AuditLog::record('assistance_review.submitted', 'assistance_review', (int) $review['id']);
        return ['success' => true];
    }

    public function moderateReview(int $reviewId, string $status, string $note, int $adminId): array
    {
        if (!in_array($status, ['approved', 'rejected'], true)) return ['success' => false, 'message' => 'Invalid review status.'];
        $review = $this->work->review($reviewId);
        if (!$review) return ['success' => false, 'message' => 'Review not found.'];
        $this->work->updateReview($reviewId, ['status' => $status, 'moderated_by' => $adminId, 'moderated_at' => date('Y-m-d H:i:s'), 'moderation_note' => trim($note) ?: null]);
        AuditLog::record('assistance_review.moderated', 'assistance_review', $reviewId, ['status' => $status]);
        return ['success' => true];
    }

    private function startIfNeeded(int $requestId): bool { $before=$this->requests->findWithDetails($requestId); if(!$before || !in_array($before['status'],['new','contacted','waiting_customer'],true)) return false; $this->db()->prepare("UPDATE assistance_requests SET status='in_progress', started_at=COALESCE(started_at,NOW()), updated_at=NOW() WHERE id=:id")->execute(['id'=>$requestId]); return true; }
    private function staffExists(int $id): bool { $stmt=$this->db()->prepare('SELECT COUNT(*) FROM users WHERE id=:id AND deleted_at IS NULL AND is_active=1');$stmt->execute(['id'=>$id]);return (int)$stmt->fetchColumn()>0; }
    private function db(): \PDO { return \App\Core\Database::connection(); }
}
