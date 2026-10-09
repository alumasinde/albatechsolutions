<?php

declare(strict_types=1);

namespace AlbaTech\Repositories;

use AlbaTech\Core\Database;
use PDO;

final class ServiceRepository
{
    public function categoriesWithServices(): array
    {
        $sql = 'SELECT c.id, c.name, c.slug, c.description,
                       s.id AS service_id, s.name AS service_name, s.slug AS service_slug,
                       s.short_description
                FROM service_categories c
                LEFT JOIN services s
                  ON s.category_id = c.id AND s.status = :published
                WHERE c.status = :category_status
                ORDER BY c.sort_order ASC, c.name ASC, s.sort_order ASC, s.name ASC';

        $statement = Database::connection()->prepare($sql);
        $statement->execute([
            'published' => 'published',
            'category_status' => 'active',
        ]);

        $categories = [];
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $id = (int) $row['id'];
            if (!isset($categories[$id])) {
                $categories[$id] = [
                    'id' => $id,
                    'name' => $row['name'],
                    'slug' => $row['slug'],
                    'description' => $row['description'],
                    'services' => [],
                ];
            }

            if ($row['service_id'] !== null) {
                $categories[$id]['services'][] = [
                    'name' => $row['service_name'],
                    'slug' => $row['service_slug'],
                    'short_description' => $row['short_description'],
                ];
            }
        }

        return array_values($categories);
    }

    public function findPublishedBySlug(string $slug): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT s.*, c.name AS category_name, c.slug AS category_slug
             FROM services s
             INNER JOIN service_categories c ON c.id = s.category_id
             WHERE s.slug = :slug AND s.status = :status AND c.status = :category_status
             LIMIT 1'
        );
        $statement->execute([
            'slug' => $slug,
            'status' => 'published',
            'category_status' => 'active',
        ]);

        $service = $statement->fetch(PDO::FETCH_ASSOC);
        return $service === false ? null : $service;
    }
}
