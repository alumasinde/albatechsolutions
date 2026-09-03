<?php

declare(strict_types=1);

namespace App\Modules\Cms\Repository;

use App\Core\BaseRepository;

final class BannerRepository extends BaseRepository
{
    protected string $table = 'banners';

    public function activeForPlacement(string $placement): array
    {
        $stmt = $this->db->prepare(
            "SELECT b.*, m.disk_path AS media_path FROM banners b
             LEFT JOIN media m ON m.id = b.media_id
             WHERE b.placement = :placement AND b.is_active = 1 AND b.deleted_at IS NULL
               AND (b.starts_at IS NULL OR b.starts_at <= NOW())
               AND (b.ends_at IS NULL OR b.ends_at >= NOW())
             ORDER BY b.sort_order ASC"
        );
        $stmt->execute(['placement' => $placement]);

        return $stmt->fetchAll();
    }

    public function allForAdmin(): array
    {
        $stmt = $this->db->query('SELECT * FROM banners WHERE deleted_at IS NULL ORDER BY placement, sort_order');

        return $stmt->fetchAll();
    }
}
