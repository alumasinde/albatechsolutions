<?php

declare(strict_types=1);

namespace App\Modules\Admin\Repository;

use App\Core\BaseRepository;

final class PermissionRepository extends BaseRepository
{
    protected string $table = 'permissions';
    protected bool $softDeletes = false;

    /**
     * Grouped by module for a friendlier role-editing UI.
     */
    public function allGroupedByModule(): array
    {
        $stmt = $this->db->query('SELECT * FROM permissions ORDER BY module ASC, name ASC');
        $rows = $stmt->fetchAll();

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['module'] ?? 'general'][] = $row;
        }

        return $grouped;
    }
}
