<?php

declare(strict_types=1);

namespace App\Modules\Admin\Service;

use App\Core\AuditLog;
use App\Core\BaseService;
use App\Core\Database;
use App\Modules\Auth\Repository\UserRepository;

final class UserService extends BaseService
{
    public function __construct(
        private readonly UserRepository $users
    ) {
    }

    /**
     * @return array{success: bool, message?: string, id?: int}
     */
    public function create(array $data, array $roleIds): array
    {
        if ($this->users->findByEmail($data['email'])) {
            return ['success' => false, 'message' => 'A user with this email already exists.'];
        }

        $userId = $this->users->create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'phone'     => $data['phone'] ?? null,
            'password'  => password_hash($data['password'], PASSWORD_BCRYPT),
            'is_active' => 1,
        ]);

        $this->assignRoles($userId, $roleIds);
        AuditLog::record('user.created', 'user', $userId);

        return ['success' => true, 'id' => $userId];
    }

    public function assignRoles(int $userId, array $roleIds): void
    {
        $db = Database::connection();
        $db->prepare('DELETE FROM user_roles WHERE user_id = :user_id')->execute(['user_id' => $userId]);

        $stmt = $db->prepare('INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)');
        foreach ($roleIds as $roleId) {
            $stmt->execute(['user_id' => $userId, 'role_id' => (int) $roleId]);
        }

        AuditLog::record('user.roles_updated', 'user', $userId, ['role_ids' => $roleIds]);
    }

    public function setActive(int $userId, bool $active): void
    {
        $this->users->update($userId, ['is_active' => $active ? 1 : 0]);
        AuditLog::record($active ? 'user.activated' : 'user.deactivated', 'user', $userId);
    }

    public function roleIdsForUser(int $userId): array
    {
        $stmt = Database::connection()->prepare('SELECT role_id FROM user_roles WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);

        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }
}
