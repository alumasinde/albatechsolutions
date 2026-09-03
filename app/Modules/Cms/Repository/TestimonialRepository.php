<?php

declare(strict_types=1);

namespace App\Modules\Cms\Repository;

use App\Core\BaseRepository;

final class TestimonialRepository extends BaseRepository
{
    protected string $table = 'testimonials';

    public function allActive(): array
    {
        $stmt = $this->db->query(
            "SELECT t.*, m.disk_path AS photo_path FROM testimonials t
             LEFT JOIN media m ON m.id = t.photo_media_id
             WHERE t.is_active = 1 AND t.deleted_at IS NULL
             ORDER BY t.sort_order ASC"
        );

        return $stmt->fetchAll();
    }

    public function allForAdmin(): array
    {
        $stmt = $this->db->query('SELECT * FROM testimonials WHERE deleted_at IS NULL ORDER BY sort_order ASC');

        return $stmt->fetchAll();
    }
}
