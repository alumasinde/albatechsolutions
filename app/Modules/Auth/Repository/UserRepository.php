<?php

declare(strict_types=1);

namespace App\Modules\Auth\Repository;

use App\Core\BaseRepository;

final class UserRepository extends BaseRepository
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE email = :email AND deleted_at IS NULL LIMIT 1'
        );
        $stmt->execute(['email' => $email]);

        return $stmt->fetch() ?: null;
    }

    public function findByGoogleId(string $googleId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE google_id = :google_id AND deleted_at IS NULL LIMIT 1'
        );
        $stmt->execute(['google_id' => $googleId]);

        return $stmt->fetch() ?: null;
    }

    public function linkGoogleId(int $userId, string $googleId): void
    {
        $this->db->prepare('UPDATE users SET google_id = :google_id WHERE id = :id')
            ->execute(['google_id' => $googleId, 'id' => $userId]);
    }

    public function allWithRoles(int $limit = 100, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            'SELECT u.id, u.name, u.email, u.phone, u.is_active, u.two_factor_enabled, u.created_at,
                    GROUP_CONCAT(r.name SEPARATOR ", ") AS role_names
             FROM users u
             LEFT JOIN user_roles ur ON ur.user_id = u.id
             LEFT JOIN roles r ON r.id = ur.role_id
             WHERE u.deleted_at IS NULL
             GROUP BY u.id
             ORDER BY u.id DESC
             LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function recordFailedLogin(int $userId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET failed_login_attempts = failed_login_attempts + 1, last_failed_login_at = NOW() WHERE id = :id'
        );
        $stmt->execute(['id' => $userId]);
    }

    public function resetFailedLogins(int $userId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET failed_login_attempts = 0, last_login_at = NOW() WHERE id = :id'
        );
        $stmt->execute(['id' => $userId]);
    }
}
