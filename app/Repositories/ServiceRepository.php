<?php

declare(strict_types=1);

namespace AlbaTech\Repositories;

use AlbaTech\Core\Database;
use PDO;

final class ServiceRepository
{
    /**
     * Public catalogue. Only published services in active categories are exposed.
     */
    public function categoriesWithServices(): array
    {
        $sql = 'SELECT c.id, c.name, c.slug, c.description,
                       s.id AS service_id, s.name AS service_name, s.slug AS service_slug,
                       s.short_description, s.pricing_type, s.pricing_amount,
                       s.pricing_label, s.is_featured
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
                    'id' => (int) $row['service_id'],
                    'name' => $row['service_name'],
                    'slug' => $row['service_slug'],
                    'short_description' => $row['short_description'],
                    'pricing_type' => $row['pricing_type'],
                    'pricing_amount' => $row['pricing_amount'],
                    'pricing_label' => $row['pricing_label'],
                    'is_featured' => (bool) $row['is_featured'],
                ];
            }
        }

        return array_values($categories);
    }

    /**
     * Homepage promotion is entirely database-driven.
     * Inactive/draft services and inactive categories are never shown.
     */
    public function featuredServices(int $limit = 6): array
    {
        $limit = max(1, min($limit, 12));
        $sql = 'SELECT s.id, s.name, s.slug, s.short_description,
                       s.pricing_type, s.pricing_amount, s.pricing_label,
                       c.name AS category_name, c.slug AS category_slug
                FROM services s
                INNER JOIN service_categories c ON c.id = s.category_id
                WHERE s.status = :published
                  AND s.is_featured = 1
                  AND c.status = :category_status
                ORDER BY s.sort_order ASC, s.name ASC
                LIMIT ' . $limit;

        $statement = Database::connection()->prepare($sql);
        $statement->execute([
            'published' => 'published',
            'category_status' => 'active',
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return list<array{slug:string}> */
    public function publishedSlugs(): array
    {
        $statement = Database::connection()->prepare(
            'SELECT s.slug FROM services s
             INNER JOIN service_categories c ON c.id = s.category_id
             WHERE s.status = :published AND c.status = :category_status
             ORDER BY s.slug ASC'
        );
        $statement->execute(['published' => 'published', 'category_status' => 'active']);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
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
