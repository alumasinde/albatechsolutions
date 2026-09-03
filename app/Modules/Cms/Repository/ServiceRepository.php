<?php

declare(strict_types=1);

namespace App\Modules\Cms\Repository;

use App\Core\BaseRepository;

final class ServiceRepository extends BaseRepository
{
    protected string $table = 'services';

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT s.*, sc.name AS category_name, sc.slug AS category_slug,
                    sx.pricing_mode AS commerce_pricing_mode, sx.customer_fee, sx.government_fee_note,
                    sx.fee_disclaimer, sx.turnaround_min_days, sx.turnaround_max_days, sx.requires_quote,
                    sx.instant_request, sx.requirements AS commerce_requirements, sx.intake_questions, sx.related_service_ids
             FROM services s
             LEFT JOIN service_categories sc ON sc.id = s.category_id
             LEFT JOIN service_commerce sx ON sx.service_id = s.id
             WHERE s.slug = :slug AND s.status = 'published' AND s.deleted_at IS NULL
             LIMIT 1"
        );
        $stmt->execute(['slug' => $slug]);

        return $stmt->fetch() ?: null;
    }

    public function allPublished(): array
    {
        $stmt = $this->db->query(
            "SELECT s.*, sc.name AS category_name, sc.slug AS category_slug,
                    sx.pricing_mode AS commerce_pricing_mode, sx.customer_fee, sx.government_fee_note,
                    sx.fee_disclaimer, sx.turnaround_min_days, sx.turnaround_max_days, sx.requires_quote,
                    sx.instant_request, sx.requirements AS commerce_requirements, sx.intake_questions, sx.related_service_ids
             FROM services s
             LEFT JOIN service_categories sc ON sc.id = s.category_id
             LEFT JOIN service_commerce sx ON sx.service_id = s.id
             WHERE s.status = 'published' AND s.deleted_at IS NULL
             ORDER BY sc.sort_order ASC, s.sort_order ASC"
        );

        return $stmt->fetchAll();
    }

    public function setStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE services SET status = :status WHERE id = :id AND deleted_at IS NULL'
        );

        return $stmt->execute([
            'status' => $status,
            'id' => $id,
        ]);
    }

    public function setFeatured(int $id, bool $featured): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE services SET is_featured = :is_featured WHERE id = :id AND deleted_at IS NULL'
        );

        return $stmt->execute([
            'is_featured' => $featured ? 1 : 0,
            'id' => $id,
        ]);
    }

    public function allForAdmin(): array
    {
        $stmt = $this->db->query(
            'SELECT s.*, sc.name AS category_name, sx.pricing_mode AS commerce_pricing_mode, sx.customer_fee, sx.government_fee_note, sx.fee_disclaimer, sx.turnaround_min_days, sx.turnaround_max_days, sx.requires_quote, sx.instant_request
             FROM services s
             LEFT JOIN service_categories sc ON sc.id = s.category_id
             LEFT JOIN service_commerce sx ON sx.service_id = s.id
             WHERE s.deleted_at IS NULL
             ORDER BY sc.sort_order ASC, s.sort_order ASC'
        );

        return $stmt->fetchAll();
    }

    /**
     * Services to show on the homepage. Prefers ones explicitly marked
     * is_featured; if none are marked, falls back to the first $limit
     * published services so the section is never empty by accident.
     */
    public function forHomepage(int $limit = 6): array
    {
        $stmt = $this->db->prepare(
            "SELECT s.*, sx.pricing_mode AS commerce_pricing_mode, sx.customer_fee, sx.government_fee_note, sx.fee_disclaimer, sx.turnaround_min_days, sx.turnaround_max_days, sx.requires_quote, sx.instant_request
             FROM services s
             LEFT JOIN service_commerce sx ON sx.service_id = s.id
             WHERE s.status = 'published' AND s.deleted_at IS NULL AND s.is_featured = 1
             ORDER BY s.sort_order ASC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $featured = $stmt->fetchAll();

        if (!empty($featured)) {
            return $featured;
        }

        $stmt = $this->db->prepare(
            "SELECT s.*, sx.pricing_mode AS commerce_pricing_mode, sx.customer_fee, sx.government_fee_note, sx.fee_disclaimer, sx.turnaround_min_days, sx.turnaround_max_days, sx.requires_quote, sx.instant_request
             FROM services s
             LEFT JOIN service_commerce sx ON sx.service_id = s.id
             WHERE s.status = 'published' AND s.deleted_at IS NULL
             ORDER BY s.sort_order ASC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
    public function findByIdPublished(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT s.*, sx.intake_questions, sx.pricing_mode AS commerce_pricing_mode, sx.customer_fee, sx.requires_quote, sx.instant_request FROM services s LEFT JOIN service_commerce sx ON sx.service_id = s.id WHERE s.id = :id AND s.status = 'published' AND s.deleted_at IS NULL LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findCommerce(int $serviceId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM service_commerce WHERE service_id = :service_id LIMIT 1');
        $stmt->execute(['service_id' => $serviceId]);
        return $stmt->fetch() ?: null;
    }

    public function upsertCommerce(int $serviceId, array $data): void
    {
        $existing = $this->findCommerce($serviceId);
        if ($existing) {
            $columns = array_keys($data);
            $assignments = implode(', ', array_map(static fn ($c) => "{$c} = :{$c}", $columns));
            $stmt = $this->db->prepare("UPDATE service_commerce SET {$assignments} WHERE service_id = :service_id");
            $stmt->execute([...$data, 'service_id' => $serviceId]);
            return;
        }
        $this->db->prepare(
            'INSERT INTO service_commerce (service_id, ' . implode(', ', array_keys($data)) . ') VALUES (:service_id, ' . implode(', ', array_map(static fn($c) => ':' . $c, array_keys($data))) . ')'
        )->execute([...$data, 'service_id' => $serviceId]);
    }

}
