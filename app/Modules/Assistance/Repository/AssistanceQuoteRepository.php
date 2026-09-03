<?php

declare(strict_types=1);

namespace App\Modules\Assistance\Repository;

use App\Core\BaseRepository;

final class AssistanceQuoteRepository extends BaseRepository
{
    protected string $table = 'assistance_quotes';
    protected bool $softDeletes = false;

    public function createQuote(array $data): int { return $this->create($data); }

    public function addItem(array $data): int {
        $columns = array_keys($data);
        $sql = 'INSERT INTO assistance_quote_items (' . implode(',', $columns) . ') VALUES (' . implode(',', array_map(fn($c) => ':' . $c, $columns)) . ')';
        $stmt = $this->db->prepare($sql); $stmt->execute($data); return (int)$this->db->lastInsertId();
    }

    public function findAdmin(int $id): ?array {
        $stmt = $this->db->prepare('SELECT q.*, ar.request_number, ar.name, ar.phone, ar.email, ar.category, ar.message, ar.service_id, u.first_name AS creator_first_name, u.last_name AS creator_last_name FROM assistance_quotes q JOIN assistance_requests ar ON ar.id=q.assistance_request_id LEFT JOIN users u ON u.id=q.created_by WHERE q.id=:id LIMIT 1');
        $stmt->execute(['id'=>$id]); return $stmt->fetch() ?: null;
    }

    public function findPublicByToken(string $rawToken): ?array {
        $hash = hash('sha256', $rawToken);
        $stmt = $this->db->prepare('SELECT q.*, ar.request_number, ar.name, ar.phone, ar.email, ar.category, ar.service_id FROM assistance_quotes q JOIN assistance_requests ar ON ar.id=q.assistance_request_id WHERE q.public_token_hash=:hash LIMIT 1');
        $stmt->execute(['hash'=>$hash]); return $stmt->fetch() ?: null;
    }

    public function items(int $quoteId): array {
        $stmt=$this->db->prepare('SELECT * FROM assistance_quote_items WHERE quote_id=:id ORDER BY sort_order ASC,id ASC');
        $stmt->execute(['id'=>$quoteId]); return $stmt->fetchAll();
    }

    public function events(int $quoteId): array {
        $stmt=$this->db->prepare('SELECT e.*, u.first_name, u.last_name FROM assistance_quote_events e LEFT JOIN users u ON u.id=e.actor_id WHERE e.quote_id=:id ORDER BY e.created_at DESC,e.id DESC');
        $stmt->execute(['id'=>$quoteId]); return $stmt->fetchAll();
    }

    public function addEvent(int $quoteId,string $event,string $actorType,?int $actorId,?string $note): int {
        $stmt=$this->db->prepare('INSERT INTO assistance_quote_events (quote_id,event,actor_type,actor_id,note) VALUES (:q,:e,:t,:a,:n)');
        $stmt->execute(['q'=>$quoteId,'e'=>$event,'t'=>$actorType,'a'=>$actorId,'n'=>$note]); return (int)$this->db->lastInsertId();
    }

    public function updateQuote(int $id,array $data): bool { return $this->update($id,$data); }

    public function latestForRequest(int $requestId): ?array {
        $stmt=$this->db->prepare('SELECT * FROM assistance_quotes WHERE assistance_request_id=:id ORDER BY id DESC LIMIT 1');
        $stmt->execute(['id'=>$requestId]); return $stmt->fetch() ?: null;
    }

    public function createPayment(array $data): int {
        $columns=array_keys($data); $sql='INSERT INTO assistance_payments ('.implode(',',$columns).') VALUES ('.implode(',',array_map(fn($c)=>':'.$c,$columns)).')';
        $stmt=$this->db->prepare($sql); $stmt->execute($data); return (int)$this->db->lastInsertId();
    }

    public function payments(int $quoteId): array {
        $stmt=$this->db->prepare('SELECT p.*, u.first_name AS verifier_first_name, u.last_name AS verifier_last_name FROM assistance_payments p LEFT JOIN users u ON u.id=p.verified_by WHERE p.quote_id=:id ORDER BY p.created_at DESC');
        $stmt->execute(['id'=>$quoteId]); return $stmt->fetchAll();
    }

    public function findPayment(int $id): ?array {
        $stmt=$this->db->prepare('SELECT p.*, q.quote_number, q.total, q.currency, q.status AS quote_status, q.assistance_request_id, ar.request_number, ar.name, ar.phone, ar.email, ar.category, ar.service_id FROM assistance_payments p JOIN assistance_quotes q ON q.id=p.quote_id JOIN assistance_requests ar ON ar.id=q.assistance_request_id WHERE p.id=:id LIMIT 1');
        $stmt->execute(['id'=>$id]); return $stmt->fetch() ?: null;
    }

    public function updatePayment(int $id,array $data): bool {
        $assign=implode(',',array_map(fn($c)=>$c.'=:'.$c,array_keys($data)));
        $stmt=$this->db->prepare('UPDATE assistance_payments SET '.$assign.' WHERE id=:id');
        return $stmt->execute([...$data,'id'=>$id]);
    }

    public function allQuotes(?string $status=null): array {
        $sql='SELECT q.*, ar.request_number, ar.name, ar.phone FROM assistance_quotes q JOIN assistance_requests ar ON ar.id=q.assistance_request_id'; $params=[];
        if($status){$sql.=' WHERE q.status=:status';$params['status']=$status;} $sql.=' ORDER BY q.created_at DESC LIMIT 250';
        $stmt=$this->db->prepare($sql);$stmt->execute($params);return $stmt->fetchAll();
    }

    public function pendingPayments(): array {
        return $this->db->query("SELECT p.*,q.quote_number,q.total,q.currency,ar.name,ar.phone FROM assistance_payments p JOIN assistance_quotes q ON q.id=p.quote_id JOIN assistance_requests ar ON ar.id=q.assistance_request_id WHERE p.status='submitted' ORDER BY p.created_at ASC")->fetchAll();
    }
    public function findPaymentByReceiptToken(string $rawToken): ?array
    {
        $stmt = $this->db->prepare('SELECT p.*, q.quote_number, q.total, q.currency, q.status AS quote_status, q.assistance_request_id, ar.request_number, ar.name, ar.phone, ar.email, ar.category, ar.service_id FROM assistance_payments p JOIN assistance_quotes q ON q.id=p.quote_id JOIN assistance_requests ar ON ar.id=q.assistance_request_id WHERE p.receipt_token_hash=:hash AND p.status=\'verified\' LIMIT 1');
        $stmt->execute(['hash' => hash('sha256', $rawToken)]);
        return $stmt->fetch() ?: null;
    }

    public function notifications(?string $status = null): array
    {
        $sql = 'SELECT n.*, ar.request_number, ar.name FROM assistance_notifications n JOIN assistance_requests ar ON ar.id=n.assistance_request_id';
        $params = [];
        if ($status) {
            $sql .= ' WHERE n.status=:status';
            $params['status'] = $status;
        }
        $sql .= ' ORDER BY n.created_at DESC LIMIT 300';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function createNotification(array $data): int
    {
        $columns = array_keys($data);
        $sql = 'INSERT INTO assistance_notifications (' . implode(',', $columns) . ') VALUES (' . implode(',', array_map(fn($c) => ':' . $c, $columns)) . ')';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function notificationExists(int $requestId, string $channel, string $event, ?string $sourceType, ?int $sourceId): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM assistance_notifications WHERE assistance_request_id=:request_id AND channel=:channel AND event=:event AND source_type <=> :source_type AND source_id <=> :source_id');
        $stmt->execute(['request_id'=>$requestId,'channel'=>$channel,'event'=>$event,'source_type'=>$sourceType,'source_id'=>$sourceId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function updateNotification(int $id, array $data): bool
    {
        $assign = implode(',', array_map(fn($c) => $c . '=:' . $c, array_keys($data)));
        $stmt = $this->db->prepare('UPDATE assistance_notifications SET ' . $assign . ' WHERE id=:id');
        return $stmt->execute([...$data, 'id'=>$id]);
    }

    public function latestVerifiedPaymentForRequest(int $requestId): ?array
    {
        $stmt=$this->db->prepare('SELECT p.*, q.quote_number, q.assistance_request_id FROM assistance_payments p JOIN assistance_quotes q ON q.id=p.quote_id WHERE q.assistance_request_id=:request_id AND p.status=\'verified\' ORDER BY p.verified_at DESC, p.id DESC LIMIT 1');
        $stmt->execute(['request_id'=>$requestId]);
        return $stmt->fetch() ?: null;
    }

}
