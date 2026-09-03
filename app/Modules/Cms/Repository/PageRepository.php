<?php

declare(strict_types=1);

namespace App\Modules\Cms\Repository;

use App\Core\BaseRepository;

final class PageRepository extends BaseRepository
{
    protected string $table = 'pages';

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM pages WHERE slug = :slug AND status = 'published' AND deleted_at IS NULL LIMIT 1"
        );
        $stmt->execute(['slug' => $slug]);

        return $stmt->fetch() ?: null;
    }

    public function findAnyBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM pages WHERE slug = :slug AND deleted_at IS NULL LIMIT 1');
        $stmt->execute(['slug' => $slug]);

        return $stmt->fetch() ?: null;
    }

    public function allForAdmin(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM pages WHERE deleted_at IS NULL ORDER BY CASE page_type WHEN 'service_intent' THEN 1 WHEN 'industry' THEN 2 WHEN 'location' THEN 3 ELSE 4 END, updated_at DESC"
        );

        return $stmt->fetchAll();
    }
}
