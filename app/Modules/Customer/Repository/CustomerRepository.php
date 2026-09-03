<?php

declare(strict_types=1);

namespace App\Modules\Customer\Repository;

use App\Core\BaseRepository;

final class CustomerRepository extends BaseRepository
{
    protected string $table = 'users';
    protected bool $softDeletes = false;

    public function dashboard(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT
                COUNT(*) AS requests_total,
                SUM(CASE WHEN status IN ('new','contacted','in_progress','waiting_customer') THEN 1 ELSE 0 END) AS active_requests,
                SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) AS completed_requests,
                SUM(CASE WHEN status='cancelled' THEN 1 ELSE 0 END) AS cancelled_requests
             FROM assistance_requests WHERE customer_user_id=:user_id"
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch() ?: [];
    }

    public function requests(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT ar.*, s.name AS service_name
             FROM assistance_requests ar
             LEFT JOIN services s ON s.id=ar.service_id
             WHERE ar.customer_user_id=:user_id
             ORDER BY ar.created_at DESC, ar.id DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function request(int $userId, int $requestId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT ar.*, s.name AS service_name, s.slug AS service_slug,
                    u.name AS assignee_name
             FROM assistance_requests ar
             LEFT JOIN services s ON s.id=ar.service_id
             LEFT JOIN users u ON u.id=ar.assigned_to
             WHERE ar.id=:id AND ar.customer_user_id=:user_id LIMIT 1'
        );
        $stmt->execute(['id' => $requestId, 'user_id' => $userId]);
        return $stmt->fetch() ?: null;
    }

    public function quotes(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT q.*, ar.request_number, ar.category
             FROM assistance_quotes q
             INNER JOIN assistance_requests ar ON ar.id=q.assistance_request_id
             WHERE ar.customer_user_id=:user_id
             ORDER BY q.created_at DESC, q.id DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function quote(int $userId, int $quoteId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT q.*, ar.request_number, ar.name, ar.phone, ar.email, ar.category, ar.message
             FROM assistance_quotes q
             INNER JOIN assistance_requests ar ON ar.id=q.assistance_request_id
             WHERE q.id=:id AND ar.customer_user_id=:user_id LIMIT 1'
        );
        $stmt->execute(['id' => $quoteId, 'user_id' => $userId]);
        return $stmt->fetch() ?: null;
    }

    public function payments(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, q.quote_number, q.total AS quote_total, ar.request_number
             FROM assistance_payments p
             INNER JOIN assistance_quotes q ON q.id=p.quote_id
             INNER JOIN assistance_requests ar ON ar.id=q.assistance_request_id
             WHERE ar.customer_user_id=:user_id
             ORDER BY p.created_at DESC, p.id DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function recentActivity(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT ar.id, ar.request_number, ar.status, ar.updated_at, ar.service_id,
                    s.name AS service_name
             FROM assistance_requests ar
             LEFT JOIN services s ON s.id=ar.service_id
             WHERE ar.customer_user_id=:user_id
             ORDER BY ar.updated_at DESC, ar.id DESC LIMIT 8"
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function linkRequest(int $requestId, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE assistance_requests
             SET customer_user_id=:user_id, updated_at=NOW()
             WHERE id=:id AND customer_user_id IS NULL'
        );
        return $stmt->execute(['id' => $requestId, 'user_id' => $userId]);
    }

    public function updateProfile(int $userId, string $name, ?string $phone): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET name=:name, phone=:phone, updated_at=NOW() WHERE id=:id AND deleted_at IS NULL');
        return $stmt->execute(['name' => $name, 'phone' => $phone, 'id' => $userId]);
    }
}
