<?php

declare(strict_types=1);

namespace App\Modules\Assistant\Repository;

use App\Core\BaseRepository;
use App\Core\Database;

final class AssistantRepository extends BaseRepository
{
    protected string $table = 'assistant_sessions';
    protected bool $softDeletes = false;

    public function createSession(string $tokenHash, ?int $userId, array $state = []): int
    {
        $stmt = $this->db->prepare('INSERT INTO assistant_sessions (session_token_hash, user_id, state) VALUES (:token, :user_id, :state)');
        $stmt->execute([
            'token' => $tokenHash,
            'user_id' => $userId,
            'state' => json_encode($state, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function findByTokenHash(string $hash): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM assistant_sessions WHERE session_token_hash=:token LIMIT 1');
        $stmt->execute(['token'=>$hash]);
        return $stmt->fetch() ?: null;
    }

    public function updateState(int $id, array $state): void
    {
        $stmt=$this->db->prepare('UPDATE assistant_sessions SET state=:state,last_activity_at=NOW() WHERE id=:id');
        $stmt->execute(['state'=>json_encode($state, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),'id'=>$id]);
    }

    public function complete(int $id): void
    {
        $this->db->prepare('UPDATE assistant_sessions SET completed_at=NOW(),last_activity_at=NOW() WHERE id=:id')->execute(['id'=>$id]);
    }

    public function addMessage(int $sessionId, string $direction, string $message, array $metadata=[]): int
    {
        $stmt=$this->db->prepare('INSERT INTO assistant_messages (session_id,direction,message,metadata) VALUES (:session_id,:direction,:message,:metadata)');
        $stmt->execute([
            'session_id'=>$sessionId,
            'direction'=>$direction,
            'message'=>$message,
            'metadata'=>$metadata ? json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) : null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function saveMatch(int $sessionId, int $serviceId, float $score, string $reason): void
    {
        $stmt=$this->db->prepare('INSERT INTO assistant_service_matches (session_id,service_id,score,reason) VALUES (:session,:service,:score,:reason) ON DUPLICATE KEY UPDATE score=VALUES(score),reason=VALUES(reason)');
        $stmt->execute(['session'=>$sessionId,'service'=>$serviceId,'score'=>$score,'reason'=>$reason]);
    }

    public function recentForAdmin(int $limit=100): array
    {
        $stmt=$this->db->prepare('SELECT s.*,u.name AS user_name,(SELECT COUNT(*) FROM assistant_messages m WHERE m.session_id=s.id) AS message_count FROM assistant_sessions s LEFT JOIN users u ON u.id=s.user_id ORDER BY s.last_activity_at DESC LIMIT :limit');
        $stmt->bindValue(':limit',$limit,\PDO::PARAM_INT); $stmt->execute(); return $stmt->fetchAll();
    }

    public function messages(int $sessionId): array
    {
        $stmt=$this->db->prepare('SELECT * FROM assistant_messages WHERE session_id=:id ORDER BY created_at ASC,id ASC');
        $stmt->execute(['id'=>$sessionId]); return $stmt->fetchAll();
    }

    public function matches(int $sessionId): array
    {
        $stmt=$this->db->prepare('SELECT m.*,s.name AS service_name,s.slug AS service_slug FROM assistant_service_matches m JOIN services s ON s.id=m.service_id WHERE m.session_id=:id ORDER BY m.score DESC');
        $stmt->execute(['id'=>$sessionId]); return $stmt->fetchAll();
    }
}
