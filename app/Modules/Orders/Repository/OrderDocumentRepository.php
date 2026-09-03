<?php

declare(strict_types=1);

namespace App\Modules\Orders\Repository;

use App\Core\Database;
use PDO;

final class OrderDocumentRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function forOrder(int $orderId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM order_documents WHERE order_id = :order_id ORDER BY created_at DESC');
        $stmt->execute(['order_id' => $orderId]);

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM order_documents WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO order_documents (order_id, uploaded_by, disk_path, original_name, mime_type, size_bytes, created_at)
             VALUES (:order_id, :uploaded_by, :disk_path, :original_name, :mime_type, :size_bytes, NOW())'
        );
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }
}
