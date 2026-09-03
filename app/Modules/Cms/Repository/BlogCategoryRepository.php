<?php

declare(strict_types=1);

namespace App\Modules\Cms\Repository;

use App\Core\BaseRepository;

final class BlogCategoryRepository extends BaseRepository
{
    protected string $table = 'blog_categories';

    public function allActive(): array
    {
        $stmt = $this->db->query('SELECT * FROM blog_categories WHERE deleted_at IS NULL ORDER BY name ASC');

        return $stmt->fetchAll();
    }
}
