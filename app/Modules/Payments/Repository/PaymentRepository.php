<?php

declare(strict_types=1);

namespace App\Modules\Payments\Repository;

use App\Core\BaseRepository;

final class PaymentRepository extends BaseRepository
{
    protected string $table = 'payments';
    protected bool $softDeletes = false;

    public function findByReference(string $reference): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE reference = :reference LIMIT 1');
        $stmt->execute(['reference' => $reference]);

        return $stmt->fetch() ?: null;
    }

    public function forContext(string $contextType, int $contextId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM payments WHERE context_type = :context_type AND context_id = :context_id ORDER BY created_at DESC'
        );
        $stmt->execute(['context_type' => $contextType, 'context_id' => $contextId]);

        return $stmt->fetchAll();
    }

    public function latestForContext(string $contextType, int $contextId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM payments WHERE context_type = :context_type AND context_id = :context_id ORDER BY created_at DESC LIMIT 1'
        );
        $stmt->execute(['context_type' => $contextType, 'context_id' => $contextId]);

        return $stmt->fetch() ?: null;
    }

    public function completedForContext(string $contextType, int $contextId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM payments WHERE context_type = :context_type AND context_id = :context_id AND status = 'completed' ORDER BY created_at DESC LIMIT 1"
        );
        $stmt->execute(['context_type' => $contextType, 'context_id' => $contextId]);

        return $stmt->fetch() ?: null;
    }

    public function revenueStats(): array
    {
        $stmt = $this->db->query(
            "SELECT
                COALESCE(SUM(amount), 0) AS total_all_time,
                COALESCE(SUM(CASE WHEN DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m') THEN amount ELSE 0 END), 0) AS total_this_month,
                COUNT(*) AS count_all_time
             FROM payments
             WHERE status = 'completed'"
        );

        return $stmt->fetch() ?: ['total_all_time' => 0, 'total_this_month' => 0, 'count_all_time' => 0];
    }

    public function allWithDetails(?string $status = null, ?string $gateway = null): array
    {
        $sql = "SELECT p.*, o.order_number, s.name AS service_name, u.name AS customer_name
                FROM payments p
                LEFT JOIN orders o ON o.id = p.context_id AND p.context_type = 'order'
                LEFT JOIN services s ON s.id = o.service_id
                LEFT JOIN users u ON u.id = o.user_id
                WHERE 1 = 1";
        $params = [];

        if ($status) {
            $sql .= ' AND p.status = :status';
            $params['status'] = $status;
        }

        if ($gateway) {
            $sql .= ' AND p.gateway = :gateway';
            $params['gateway'] = $gateway;
        }

        $sql .= ' ORDER BY p.created_at DESC LIMIT 200';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function pendingBankTransfers(): array
    {
        $stmt = $this->db->query(
            "SELECT p.*, o.order_number, s.name AS service_name, u.name AS customer_name
             FROM payments p
             INNER JOIN orders o ON o.id = p.context_id AND p.context_type = 'order'
             INNER JOIN services s ON s.id = o.service_id
             INNER JOIN users u ON u.id = o.user_id
             WHERE p.method = 'bank_transfer' AND p.status = 'pending'
             ORDER BY p.created_at ASC"
        );

        return $stmt->fetchAll();
    }
}
