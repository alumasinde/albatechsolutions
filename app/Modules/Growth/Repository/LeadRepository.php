<?php

declare(strict_types=1);

namespace App\Modules\Growth\Repository;

use App\Core\BaseRepository;

final class LeadRepository extends BaseRepository
{
    protected string $table = 'quote_requests';
    protected bool $softDeletes = false;

    public function allForAdmin(?string $status = null): array
    {
        $sql = 'SELECT q.*, s.name AS service_name FROM quote_requests q LEFT JOIN services s ON s.id = q.service_id';
        $params = [];
        if ($status && in_array($status, ['new','contacted','qualified','quote_sent','won','lost','spam'], true)) {
            $sql .= ' WHERE q.status = :status';
            $params['status'] = $status;
        }
        $sql .= ' ORDER BY q.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findWithService(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT q.*, s.name AS service_name FROM quote_requests q LEFT JOIN services s ON s.id = q.service_id WHERE q.id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function counts(): array
    {
        $rows = $this->db->query('SELECT status, COUNT(*) AS total FROM quote_requests GROUP BY status')->fetchAll();
        $counts = array_fill_keys(['new','contacted','qualified','quote_sent','won','lost','spam'], 0);
        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['total'];
        }
        return $counts;
    }
}
