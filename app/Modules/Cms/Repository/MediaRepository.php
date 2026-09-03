<?php

declare(strict_types=1);

namespace App\Modules\Cms\Repository;

use App\Core\BaseRepository;

final class MediaRepository extends BaseRepository
{
    protected string $table = 'media';

    public function paginate(int $page = 1, int $perPage = 30): array
    {
        $offset = max(0, ($page - 1) * $perPage);

        $stmt = $this->db->prepare(
            'SELECT * FROM media WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
