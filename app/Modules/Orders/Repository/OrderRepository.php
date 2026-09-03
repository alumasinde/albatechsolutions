<?php

declare(strict_types=1);

namespace App\Modules\Orders\Repository;

use App\Core\BaseRepository;
use PDO;

final class OrderRepository extends BaseRepository
{
    protected string $table = 'orders';
    protected bool $softDeletes = false;

    public function findWithDetails(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, s.name AS service_name, s.slug AS service_slug,
                    u.name AS customer_name, u.email AS customer_email, u.phone AS customer_phone
             FROM orders o
             INNER JOIN services s ON s.id = o.service_id
             INNER JOIN users u ON u.id = o.user_id
             WHERE o.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function forCustomer(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, s.name AS service_name, s.slug AS service_slug
             FROM orders o
             INNER JOIN services s ON s.id = o.service_id
             WHERE o.user_id = :user_id
             ORDER BY o.created_at DESC'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function belongsToUser(int $orderId, int $userId): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM orders WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $orderId, 'user_id' => $userId]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function allForAdmin(?string $status = null): array
    {
        $sql = 'SELECT o.*, s.name AS service_name, u.name AS customer_name, u.email AS customer_email
                FROM orders o
                INNER JOIN services s ON s.id = o.service_id
                INNER JOIN users u ON u.id = o.user_id';

        $params = [];
        if ($status) {
            $sql .= ' WHERE o.status = :status';
            $params['status'] = $status;
        }

        $sql .= ' ORDER BY o.created_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function statusHistory(int $orderId): array
    {
        $stmt = $this->db->prepare(
            'SELECT osh.*, u.name AS changed_by_name
             FROM order_status_history osh
             LEFT JOIN users u ON u.id = osh.changed_by
             WHERE osh.order_id = :order_id
             ORDER BY osh.created_at ASC'
        );
        $stmt->execute(['order_id' => $orderId]);

        return $stmt->fetchAll();
    }

    public function recordStatusChange(int $orderId, string $status, ?int $changedBy, ?string $note = null): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO order_status_history (order_id, status, note, changed_by, created_at)
             VALUES (:order_id, :status, :note, :changed_by, NOW())'
        );
        $stmt->execute([
            'order_id'   => $orderId,
            'status'     => $status,
            'note'       => $note,
            'changed_by' => $changedBy,
        ]);
    }

    public function setOrderNumber(int $orderId, string $orderNumber): void
    {
        $this->db->prepare('UPDATE orders SET order_number = :order_number WHERE id = :id')
            ->execute(['order_number' => $orderNumber, 'id' => $orderId]);
    }
}
