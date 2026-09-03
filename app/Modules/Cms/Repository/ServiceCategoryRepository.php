<?php

declare(strict_types=1);

namespace App\Modules\Cms\Repository;

use App\Core\BaseRepository;

final class ServiceCategoryRepository extends BaseRepository
{
    protected string $table = 'service_categories';

    public function allActive(): array
    {
        $stmt = $this->db->query(
            'SELECT * FROM service_categories WHERE deleted_at IS NULL ORDER BY sort_order ASC'
        );

        return $stmt->fetchAll();
    }

    /**
     * Categories with their published services grouped underneath,
     * for the public catalogue index.
     */
    public function withPublishedServices(): array
    {
        $categories = $this->allActive();

        $stmt = $this->db->query(
            "SELECT * FROM services WHERE status = 'published' AND deleted_at IS NULL ORDER BY sort_order ASC"
        );
        $services = $stmt->fetchAll();

        foreach ($categories as &$category) {
            $category['services'] = array_values(array_filter(
                $services,
                static fn ($s) => (int) $s['category_id'] === (int) $category['id']
            ));
        }
        unset($category);

        $uncategorized = array_values(array_filter(
            $services,
            static fn ($s) => $s['category_id'] === null
        ));

        if ($uncategorized) {
            $categories[] = ['id' => null, 'name' => 'Other Services', 'services' => $uncategorized];
        }

        return array_values(array_filter($categories, static fn ($c) => !empty($c['services'])));
    }
}
