<?php

declare(strict_types=1);

namespace App\Modules\Growth\Repository;

use App\Core\BaseRepository;

final class ProjectRepository extends BaseRepository
{
    protected string $table = 'projects';

    public function allForAdmin(): array
    {
        return $this->db->query('SELECT * FROM projects WHERE deleted_at IS NULL ORDER BY sort_order ASC, created_at DESC')->fetchAll();
    }

    public function allPublished(): array
    {
        return $this->db->query("SELECT * FROM projects WHERE status = 'published' AND deleted_at IS NULL ORDER BY featured DESC, sort_order ASC, published_at DESC, created_at DESC")->fetchAll();
    }

    public function featured(int $limit = 3): array
    {
        $stmt = $this->db->prepare("SELECT * FROM projects WHERE status = 'published' AND deleted_at IS NULL ORDER BY featured DESC, sort_order ASC, published_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findPublishedBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM projects WHERE slug = :slug AND status = 'published' AND deleted_at IS NULL LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch() ?: null;
    }
}
