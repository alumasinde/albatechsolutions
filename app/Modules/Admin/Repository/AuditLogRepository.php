<?php

declare(strict_types=1);

namespace App\Modules\Admin\Repository;

use App\Core\Database;
use PDO;

final class AuditLogRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function paginate(int $page = 1, int $perPage = 50): array
    {
        $offset = max(0, ($page - 1) * $perPage);

        $stmt = $this->db->prepare(
            'SELECT al.*, u.name AS user_name, u.email AS user_email
             FROM audit_logs al
             LEFT JOIN users u ON u.id = al.user_id
             ORDER BY al.created_at DESC
             LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM audit_logs')->fetchColumn();
    }
}
