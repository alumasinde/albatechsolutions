<?php

declare(strict_types=1);

namespace App\Modules\Cms\Service;

use App\Core\AuditLog;
use App\Core\BaseService;
use App\Modules\Cms\Repository\TestimonialRepository;

final class TestimonialService extends BaseService
{
    public function __construct(
        private readonly TestimonialRepository $testimonials
    ) {
    }

    public function create(array $data): int
    {
        $id = $this->testimonials->create($this->preparePayload($data));
        AuditLog::record('testimonial.created', 'testimonial', $id);

        return $id;
    }

    public function update(int $id, array $data): void
    {
        $this->testimonials->update($id, $this->preparePayload($data));
        AuditLog::record('testimonial.updated', 'testimonial', $id);
    }

    public function delete(int $id): void
    {
        $this->testimonials->delete($id);
        AuditLog::record('testimonial.deleted', 'testimonial', $id);
    }

    private function preparePayload(array $data): array
    {
        return [
            'client_name'    => $data['client_name'],
            'client_title'   => $data['client_title'] ?? null,
            'client_company' => $data['client_company'] ?? null,
            'photo_media_id' => !empty($data['photo_media_id']) ? $data['photo_media_id'] : null,
            'quote'          => $data['quote'],
            'rating'         => $data['rating'] !== '' ? (int) $data['rating'] : null,
            'sort_order'     => (int) ($data['sort_order'] ?? 0),
            'is_active'      => !empty($data['is_active']) ? 1 : 0,
        ];
    }
}
