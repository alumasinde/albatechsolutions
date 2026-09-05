<?php

declare(strict_types=1);

namespace App\Modules\Assistance\Repository;

use App\Core\BaseRepository;

final class AssistanceRequestRepository extends BaseRepository
{
    protected string $table = 'assistance_requests';
    protected bool $softDeletes = false;

    public function createRequest(array $data): int
    {
        return $this->create($data);
    }

    public function allForAdmin(?string $status = null): array
    {
        $sql = 'SELECT ar.*, s.name AS service_name, u.name AS assignee_name
                FROM assistance_requests ar
                LEFT JOIN services s ON s.id = ar.service_id
                LEFT JOIN users u ON u.id = ar.assigned_to';
        $params = [];
        $allowed = ['new','contacted','in_progress','waiting_customer','completed','cancelled','spam'];
        if ($status && in_array($status, $allowed, true)) {
            $sql .= ' WHERE ar.status = :status';
            $params['status'] = $status;
        }
        $sql .= ' ORDER BY ar.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function paginate(int $page = 1, int $perPage = 20, ?string $status = null): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;
        $params = [];

        $where = '';
        $allowed = ['new','contacted','in_progress','waiting_customer','completed','cancelled','spam'];
        if ($status !== null && in_array($status, $allowed, true)) {
            $where = ' WHERE ar.status = :status';
            $params['status'] = $status;
        }

        $count = $this->db->prepare('SELECT COUNT(*) FROM assistance_requests ar' . $where);
        $count->execute($params);
        $total = (int) $count->fetchColumn();

        $sql = 'SELECT ar.*, s.name AS service_name, u.name AS assignee_name
                FROM assistance_requests ar
                LEFT JOIN services s ON s.id = ar.service_id
                LEFT JOIN users u ON u.id = ar.assigned_to'
                . $where . ' ORDER BY ar.created_at DESC LIMIT ' . $perPage . ' OFFSET ' . $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(),
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => (int) max(1, ceil($total / $perPage)),
            ],
        ];
    }

    public function findWithDetails(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT ar.*, s.name AS service_name, s.slug AS service_slug,
                    u.name AS assignee_name
             FROM assistance_requests ar
             LEFT JOIN services s ON s.id = ar.service_id
             LEFT JOIN users u ON u.id = ar.assigned_to
             WHERE ar.id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function counts(): array
    {
        $rows = $this->db->query('SELECT status, COUNT(*) AS total FROM assistance_requests GROUP BY status')->fetchAll();
        $counts = array_fill_keys(['new','contacted','in_progress','waiting_customer','completed','cancelled','spam'], 0);
        foreach ($rows as $row) {
            if (array_key_exists($row['status'], $counts)) $counts[$row['status']] = (int) $row['total'];
        }
        return $counts;
    }

    public function updateStatus(int $id, string $status, ?string $notes = null): bool
    {
        $stmt = $this->db->prepare('UPDATE assistance_requests SET status = :status, admin_notes = :notes, updated_at = NOW() WHERE id = :id');
        return $stmt->execute(['status' => $status, 'notes' => $notes, 'id' => $id]);
    }

    public function addHistory(int $requestId, ?string $fromStatus, string $toStatus, ?int $changedBy, ?string $note): int
    {
        return $this->db->prepare(
            'INSERT INTO assistance_request_history (assistance_request_id, from_status, to_status, changed_by, note)
             VALUES (:request_id, :from_status, :to_status, :changed_by, :note)'
        )->execute([
            'request_id' => $requestId,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by' => $changedBy,
            'note' => $note,
        ]) ? (int) $this->db->lastInsertId() : 0;
    }
}
