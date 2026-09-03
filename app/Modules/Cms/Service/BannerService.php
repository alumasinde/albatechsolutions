<?php

declare(strict_types=1);

namespace App\Modules\Cms\Service;

use App\Core\AuditLog;
use App\Core\BaseService;
use App\Modules\Cms\Repository\BannerRepository;

final class BannerService extends BaseService
{
    public function __construct(
        private readonly BannerRepository $banners
    ) {
    }

    public function create(array $data): int
    {
        $id = $this->banners->create($this->preparePayload($data));
        AuditLog::record('banner.created', 'banner', $id);

        return $id;
    }

    public function update(int $id, array $data): void
    {
        $this->banners->update($id, $this->preparePayload($data));
        AuditLog::record('banner.updated', 'banner', $id);
    }

    public function delete(int $id): void
    {
        $this->banners->delete($id);
        AuditLog::record('banner.deleted', 'banner', $id);
    }

    private function preparePayload(array $data): array
    {
        return [
            'title'      => $data['title'] ?? null,
            'subtitle'   => $data['subtitle'] ?? null,
            'media_id'   => !empty($data['media_id']) ? $data['media_id'] : null,
            'cta_label'  => $data['cta_label'] ?? null,
            'cta_url'    => $data['cta_url'] ?? null,
            'placement'  => $data['placement'] ?? 'homepage_hero',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active'  => !empty($data['is_active']) ? 1 : 0,
            'starts_at'  => !empty($data['starts_at']) ? $data['starts_at'] : null,
            'ends_at'    => !empty($data['ends_at']) ? $data['ends_at'] : null,
        ];
    }
}
