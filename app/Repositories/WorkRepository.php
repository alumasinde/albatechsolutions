<?php

declare(strict_types=1);

namespace AlbaTech\Repositories;

use AlbaTech\Core\Database;

final class WorkRepository
{
    public function featured(int $limit = 8): array
    {
        $limit = max(1, min(12, $limit));
        $stmt = Database::connection()->query("SELECT w.id,w.title,w.slug,w.description,w.cover_image,w.kind,w.lifecycle_status,w.platform,c.name AS category_name FROM work_projects w JOIN service_categories c ON c.id=w.category_id WHERE w.status='published' AND w.kind='project' AND w.is_featured=1 ORDER BY w.sort_order,w.updated_at DESC LIMIT {$limit}");
        return $stmt->fetchAll();
    }

    public function products(int $limit = 6): array
    {
        $limit = max(1, min(12, $limit));
        $stmt = Database::connection()->query("SELECT w.id,w.title,w.slug,w.description,w.cover_image,w.kind,w.lifecycle_status,w.platform,w.download_url,w.live_url,c.name AS category_name FROM work_projects w JOIN service_categories c ON c.id=w.category_id WHERE w.status='published' AND w.kind='product' ORDER BY w.is_featured DESC,w.sort_order,w.updated_at DESC LIMIT {$limit}");
        return $stmt->fetchAll();
    }

    public function relatedToCategory(int $categoryId, int $excludeId = 0, int $limit = 3): array
    {
        $limit=max(1,min(6,$limit));
        $stmt=Database::connection()->prepare("SELECT w.id,w.title,w.slug,w.description,w.cover_image,w.kind,w.lifecycle_status,c.name AS category_name FROM work_projects w JOIN service_categories c ON c.id=w.category_id WHERE w.status='published' AND w.category_id=:category AND w.id<>:exclude ORDER BY w.is_featured DESC,w.sort_order,w.updated_at DESC LIMIT {$limit}");
        $stmt->execute(['category'=>$categoryId,'exclude'=>$excludeId]); return $stmt->fetchAll();
    }
}
