<?php

declare(strict_types=1);

namespace App\Modules\Auth\Repository;

use App\Core\BaseRepository;

final class UserTokenRepository extends BaseRepository
{
    protected string $table = 'user_tokens';
    protected bool $softDeletes = false;

    /**
     * Creates a token, returns the raw (unhashed) value to send to the
     * user — only the hash is ever persisted.
     */
    public function issue(int $userId, string $type, int $ttlMinutes): string
    {
        // A fresh request supersedes any earlier unused token of the same
        // type, so an old email link can't be used after a newer one was
        // requested.
        $this->invalidateExisting($userId, $type);

        $raw = bin2hex(random_bytes(32));

        $this->create([
            'user_id'    => $userId,
            'type'       => $type,
            'token_hash' => hash('sha256', $raw),
            'expires_at' => date('Y-m-d H:i:s', time() + $ttlMinutes * 60),
        ]);

        return $raw;
    }

    /**
     * Returns the token row if valid (right type, not expired, not used),
     * otherwise null. Does not consume it — call consume() after the
     * action it guards actually succeeds.
     */
    public function findValid(string $rawToken, string $type): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM user_tokens
             WHERE token_hash = :hash AND type = :type AND used_at IS NULL AND expires_at > NOW()
             LIMIT 1"
        );
        $stmt->execute(['hash' => hash('sha256', $rawToken), 'type' => $type]);

        return $stmt->fetch() ?: null;
    }

    public function consume(int $tokenId): void
    {
        $this->db->prepare('UPDATE user_tokens SET used_at = NOW() WHERE id = :id')
            ->execute(['id' => $tokenId]);
    }

    private function invalidateExisting(int $userId, string $type): void
    {
        $this->db->prepare(
            "UPDATE user_tokens SET used_at = NOW() WHERE user_id = :user_id AND type = :type AND used_at IS NULL"
        )->execute(['user_id' => $userId, 'type' => $type]);
    }
}
