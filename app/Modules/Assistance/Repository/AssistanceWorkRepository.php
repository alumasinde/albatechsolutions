<?php

declare(strict_types=1);

namespace App\Modules\Assistance\Repository;

use App\Core\BaseRepository;
use App\Core\Database;

final class AssistanceWorkRepository extends BaseRepository
{
    protected string $table = 'assistance_tasks';
    protected bool $softDeletes = false;

    public function activeStaff(): array
    {
        return $this->db->query('SELECT id, name, email, phone FROM users WHERE deleted_at IS NULL AND is_active = 1 ORDER BY name ASC')->fetchAll();
    }

    public function tasks(int $requestId): array
    {
        $stmt = $this->db->prepare('SELECT t.*, u.name AS assignee_name FROM assistance_tasks t LEFT JOIN users u ON u.id=t.assigned_to WHERE t.assistance_request_id=:id ORDER BY t.sort_order ASC, t.id ASC');
        $stmt->execute(['id'=>$requestId]);
        return $stmt->fetchAll();
    }

    public function createTask(array $data): int { return $this->create($data); }

    public function updateTask(int $id, array $data): bool { return $this->update($id, $data); }

    public function task(int $id): ?array
    {
        $stmt=$this->db->prepare('SELECT * FROM assistance_tasks WHERE id=:id LIMIT 1');
        $stmt->execute(['id'=>$id]); return $stmt->fetch() ?: null;
    }

    public function updates(int $requestId, bool $customerOnly=false): array
    {
        $sql='SELECT au.*, u.name AS author_name FROM assistance_updates au LEFT JOIN users u ON u.id=au.author_id WHERE au.assistance_request_id=:id';
        if($customerOnly) $sql.=' AND au.visibility=\'customer\'';
        $sql.=' ORDER BY au.created_at DESC, au.id DESC';
        $stmt=$this->db->prepare($sql); $stmt->execute(['id'=>$requestId]); return $stmt->fetchAll();
    }

    public function addUpdate(array $data): int
    {
        $columns=array_keys($data); $sql='INSERT INTO assistance_updates ('.implode(',',$columns).') VALUES ('.implode(',',array_map(fn($c)=>':'.$c,$columns)).')';
        $stmt=$this->db->prepare($sql); $stmt->execute($data); return (int)$this->db->lastInsertId();
    }

    public function reviewByRequest(int $requestId): ?array
    {
        $stmt=$this->db->prepare('SELECT * FROM assistance_reviews WHERE assistance_request_id=:id LIMIT 1'); $stmt->execute(['id'=>$requestId]); return $stmt->fetch() ?: null;
    }

    public function reviewByToken(string $token): ?array
    {
        $stmt=$this->db->prepare('SELECT r.*, ar.request_number, ar.name, ar.service_id, ar.status AS request_status FROM assistance_reviews r JOIN assistance_requests ar ON ar.id=r.assistance_request_id WHERE r.public_token_hash=:hash LIMIT 1');
        $stmt->execute(['hash'=>hash('sha256',$token)]); return $stmt->fetch() ?: null;
    }

    public function createReview(array $data): int
    {
        $columns=array_keys($data); $sql='INSERT INTO assistance_reviews ('.implode(',',$columns).') VALUES ('.implode(',',array_map(fn($c)=>':'.$c,$columns)).')';
        $stmt=$this->db->prepare($sql); $stmt->execute($data); return (int)$this->db->lastInsertId();
    }

    public function pendingReviews(): array
    {
        return $this->db->query('SELECT r.*, ar.request_number FROM assistance_reviews r JOIN assistance_requests ar ON ar.id=r.assistance_request_id WHERE r.status=\'pending\' ORDER BY r.reviewed_at ASC')->fetchAll();
    }

    public function updateReview(int $id, array $data): bool
    {
        $assign=implode(',',array_map(fn($c)=>$c.'=:'.$c,array_keys($data))); $stmt=$this->db->prepare('UPDATE assistance_reviews SET '.$assign.' WHERE id=:id'); return $stmt->execute([...$data,'id'=>$id]);
    }

    public function review(int $id): ?array
    {
        $stmt=$this->db->prepare('SELECT r.*, ar.request_number FROM assistance_reviews r JOIN assistance_requests ar ON ar.id=r.assistance_request_id WHERE r.id=:id LIMIT 1'); $stmt->execute(['id'=>$id]); return $stmt->fetch() ?: null;
    }
}
