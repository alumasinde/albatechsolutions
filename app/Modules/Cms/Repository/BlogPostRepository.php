<?php

declare(strict_types=1);

namespace App\Modules\Cms\Repository;

use App\Core\BaseRepository;
use PDO;

final class BlogPostRepository extends BaseRepository
{
    protected string $table = 'blog_posts';

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT bp.*, bc.name AS category_name, bc.slug AS category_slug, u.name AS author_name
             FROM blog_posts bp
             LEFT JOIN blog_categories bc ON bc.id = bp.category_id
             LEFT JOIN users u ON u.id = bp.author_id
             WHERE bp.slug = :slug AND bp.status = 'published' AND bp.deleted_at IS NULL
             LIMIT 1"
        );
        $stmt->execute(['slug' => $slug]);

        return $stmt->fetch() ?: null;
    }

    public function paginatePublished(int $page = 1, int $perPage = 9, ?string $categorySlug = null): array
    {
        $offset = max(0, ($page - 1) * $perPage);

        $sql = "SELECT bp.*, bc.name AS category_name, bc.slug AS category_slug
                FROM blog_posts bp
                LEFT JOIN blog_categories bc ON bc.id = bp.category_id
                WHERE bp.status = 'published' AND bp.deleted_at IS NULL";

        $params = [];
        if ($categorySlug) {
            $sql .= ' AND bc.slug = :category_slug';
            $params['category_slug'] = $categorySlug;
        }

        $sql .= ' ORDER BY bp.published_at DESC LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function allForAdmin(): array
    {
        $stmt = $this->db->query(
            'SELECT bp.*, bc.name AS category_name
             FROM blog_posts bp
             LEFT JOIN blog_categories bc ON bc.id = bp.category_id
             WHERE bp.deleted_at IS NULL
             ORDER BY bp.updated_at DESC'
        );

        return $stmt->fetchAll();
    }
}
