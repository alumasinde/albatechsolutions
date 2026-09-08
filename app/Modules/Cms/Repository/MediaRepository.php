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

    /** @return array<int, array<string, mixed>> */
    public function imageLibrary(int $limit = 100): array
    {
        $limit = max(1, min($limit, 500));
        $stmt = $this->db->query(
            "SELECT id, disk_path, original_name, mime_type, size_bytes, purpose, created_at
             FROM media
             WHERE deleted_at IS NULL
               AND mime_type IN ('image/jpeg', 'image/png', 'image/webp', 'image/svg+xml')
             ORDER BY created_at DESC, id DESC
             LIMIT {$limit}"
        );

        return $stmt->fetchAll();
    }

    public function findImage(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id, disk_path, original_name, mime_type, size_bytes, purpose, created_at
             FROM media
             WHERE id = :id AND deleted_at IS NULL
               AND mime_type IN ('image/jpeg', 'image/png', 'image/webp', 'image/svg+xml')
             LIMIT 1"
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }
}
