<?php

declare(strict_types=1);

namespace App\Modules\Cms\Service;

use App\Core\AuditLog;
use App\Core\BaseService;
use App\Core\Database;
use App\Core\Helpers\Sanitizer;
use App\Modules\Cms\Repository\ServiceRepository;

final class ServiceCatalogueService extends BaseService
{
    public function __construct(
        private readonly ServiceRepository $services
    ) {
    }

    public function create(array $data): int
    {
        $slug = $this->uniqueSlug($data['slug'] ?? $data['name']);

        $id = $this->services->create($this->preparePayload($data, $slug));
        $this->services->upsertCommerce($id, $this->prepareCommercePayload($data));
        AuditLog::record('service.created', 'service', $id);

        return $id;
    }

    public function update(int $id, array $data): void
    {
        $existing = $this->services->find($id);
        $slug = $existing && $existing['slug'] === ($data['slug'] ?? null)
            ? $data['slug']
            : $this->uniqueSlug($data['slug'] ?? $data['name'], $id);

        $this->services->update($id, $this->preparePayload($data, $slug));
        $this->services->upsertCommerce($id, $this->prepareCommercePayload($data));
        AuditLog::record('service.updated', 'service', $id);
    }

    public function delete(int $id): void
    {
        $this->services->delete($id);
        AuditLog::record('service.deleted', 'service', $id);
    }

    public function toggleStatus(int $id): string
    {
        $service = $this->services->find($id);

        if (!$service) {
            throw new \RuntimeException('Service not found.');
        }

        $nextStatus = ($service['status'] ?? 'draft') === 'published' ? 'draft' : 'published';
        $this->services->setStatus($id, $nextStatus);

        AuditLog::record(
            $nextStatus === 'published' ? 'service.activated' : 'service.deactivated',
            'service',
            $id
        );

        return $nextStatus;
    }

    public function toggleHomepage(int $id): bool
    {
        $service = $this->services->find($id);

        if (!$service) {
            throw new \RuntimeException('Service not found.');
        }

        if (($service['status'] ?? 'draft') !== 'published') {
            throw new \RuntimeException('Activate the service before displaying it on the homepage.');
        }

        $enabled = empty($service['is_featured']);
        $this->services->setFeatured($id, $enabled);

        AuditLog::record(
            $enabled ? 'service.homepage_enabled' : 'service.homepage_disabled',
            'service',
            $id
        );

        return $enabled;
    }

    private function preparePayload(array $data, string $slug): array
    {
        $commerceMode = in_array(($data['commerce_pricing_mode'] ?? ''), ['fixed','starting_from','quote','free'], true)
            ? $data['commerce_pricing_mode'] : null;
        $priceType = $commerceMode === 'free' ? 'quote' : ($commerceMode ?? (in_array($data['price_type'] ?? 'quote', ['fixed','starting_from','quote'], true) ? $data['price_type'] : 'quote'));
        $cataloguePrice = $commerceMode !== null ? trim((string)($data['customer_fee'] ?? '')) : trim((string)($data['price'] ?? ''));

        return [
            'category_id'      => !empty($data['category_id']) ? $data['category_id'] : null,
            'name'             => $data['name'],
            'slug'             => $slug,
            'summary'          => $data['summary'] ?? null,
            'description'      => Sanitizer::cleanRichText($data['description'] ?? ''),
            'icon'             => $data['icon'] ?? null,
            'price_type'       => $priceType,
            'price'            => $priceType === 'quote' ? null : ($cataloguePrice !== '' ? (float) $cataloguePrice : null),
            'status'           => in_array($data['status'] ?? 'draft', ['draft', 'published'], true) ? $data['status'] : 'draft',
            'is_featured'      => !empty($data['is_featured']) ? 1 : 0,
            'meta_title'       => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'sort_order'       => (int) ($data['sort_order'] ?? 0),
        ];
    }

    private function prepareCommercePayload(array $data): array
    {
        $mode = in_array(($data['commerce_pricing_mode'] ?? 'quote'), ['fixed','starting_from','quote','free'], true)
            ? $data['commerce_pricing_mode'] : 'quote';
        $fee = trim((string)($data['customer_fee'] ?? ''));
        $requirements = $this->jsonArray($data['commerce_requirements'] ?? '');
        $questions = $this->jsonQuestions($data['intake_questions'] ?? '');
        $related = $this->jsonInts($data['related_service_ids'] ?? '');
        return [
            'pricing_mode' => $mode,
            'customer_fee' => ($mode === 'free' || $fee === '') ? null : max(0, (float)$fee),
            'government_fee_note' => trim((string)($data['government_fee_note'] ?? '')) ?: null,
            'fee_disclaimer' => trim((string)($data['fee_disclaimer'] ?? '')) ?: null,
            'turnaround_min_days' => $this->nullableInt($data['turnaround_min_days'] ?? null),
            'turnaround_max_days' => $this->nullableInt($data['turnaround_max_days'] ?? null),
            'requires_quote' => !empty($data['requires_quote']) ? 1 : 0,
            'instant_request' => !empty($data['instant_request']) ? 1 : 0,
            'active' => !empty($data['commerce_active']) ? 1 : 0,
            'requirements' => json_encode($requirements, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'intake_questions' => json_encode($questions, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'related_service_ids' => json_encode($related, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'internal_notes' => trim((string)($data['commerce_internal_notes'] ?? '')) ?: null,
        ];
    }

    private function jsonArray(mixed $value): array
    {
        if (is_array($value)) return array_values(array_filter(array_map('trim', $value), static fn($v) => $v !== ''));
        $decoded = json_decode(trim((string)$value), true);
        return is_array($decoded) ? array_values(array_filter(array_map('strval', $decoded), static fn($v) => trim($v) !== '')) : [];
    }

    private function jsonQuestions(mixed $value): array
    {
        if (is_array($value)) return $value;
        $raw = trim((string)$value);
        if ($raw === '') return [];
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) return $decoded;
        $questions = [];
        foreach (preg_split('/\R/', $raw) ?: [] as $line) {
            $parts = array_map('trim', explode('|', $line, 5));
            if (count($parts) < 2 || $parts[0] === '' || $parts[1] === '') continue;
            $type = in_array($parts[2] ?? 'text', ['text','textarea','select'], true) ? $parts[2] : 'text';
            $questions[] = [
                'key' => $parts[0],
                'label' => $parts[1],
                'type' => $type,
                'required' => (($parts[3] ?? 'optional') === 'required'),
                'help' => $parts[4] ?? null,
            ];
        }
        return $questions;
    }

    private function jsonInts(mixed $value): array
    {
        if (is_array($value)) return array_values(array_filter(array_map('intval', $value), static fn($v) => $v > 0));
        $decoded = json_decode(trim((string)$value), true);
        return is_array($decoded) ? array_values(array_filter(array_map('intval', $decoded), static fn($v) => $v > 0)) : [];
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || trim((string)$value) === '') return null;
        return max(0, (int)$value);
    }

    private function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Sanitizer::slug($source);
        $slug = $base;
        $suffix = 1;
        $db = Database::connection();

        while (true) {
            $stmt = $db->prepare('SELECT id FROM services WHERE slug = :slug AND deleted_at IS NULL LIMIT 1');
            $stmt->execute(['slug' => $slug]);
            $existing = $stmt->fetch();

            if (!$existing || ($ignoreId !== null && (int) $existing['id'] === $ignoreId)) {
                return $slug;
            }
            $slug = $base . '-' . (++$suffix);
        }
    }
}
