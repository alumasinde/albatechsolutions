<?php

declare(strict_types=1);

namespace App\Modules\Cms\Repository;

use App\Core\BaseRepository;

final class FaqRepository extends BaseRepository
{
    protected string $table = 'faqs';

    public function allActive(): array
    {
        $stmt = $this->db->query(
            'SELECT * FROM faqs WHERE is_active = 1 AND deleted_at IS NULL ORDER BY sort_order ASC'
        );

        return $stmt->fetchAll();
    }

    public function allForAdmin(): array
    {
        $stmt = $this->db->query('SELECT * FROM faqs WHERE deleted_at IS NULL ORDER BY sort_order ASC');

        return $stmt->fetchAll();
    }
}
