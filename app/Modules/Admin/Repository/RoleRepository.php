<?php

declare(strict_types=1);

namespace App\Modules\Admin\Repository;

use App\Core\BaseRepository;

final class RoleRepository extends BaseRepository
{
    protected string $table = 'roles';
    protected bool $softDeletes = true;

    public function allWithPermissionCount(): array
    {
        $stmt = $this->db->query(
            'SELECT r.*, COUNT(rp.permission_id) AS permission_count
             FROM roles r
             LEFT JOIN role_permissions rp ON rp.role_id = r.id
             WHERE r.deleted_at IS NULL
             GROUP BY r.id
             ORDER BY r.id ASC'
        );

        return $stmt->fetchAll();
    }

    public function permissionIdsForRole(int $roleId): array
    {
        $stmt = $this->db->prepare('SELECT permission_id FROM role_permissions WHERE role_id = :role_id');
        $stmt->execute(['role_id' => $roleId]);

        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    public function syncPermissions(int $roleId, array $permissionIds): void
    {
        $this->db->beginTransaction();

        try {
            $this->db->prepare('DELETE FROM role_permissions WHERE role_id = :role_id')
                ->execute(['role_id' => $roleId]);

            $stmt = $this->db->prepare(
                'INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)'
            );

            foreach ($permissionIds as $permissionId) {
                $stmt->execute(['role_id' => $roleId, 'permission_id' => (int) $permissionId]);
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
