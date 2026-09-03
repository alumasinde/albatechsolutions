<?php

declare(strict_types=1);

namespace App\Modules\Cms\Service;

use App\Core\AuditLog;
use App\Core\BaseService;
use App\Modules\Cms\Repository\FaqRepository;

final class FaqService extends BaseService
{
    public function __construct(
        private readonly FaqRepository $faqs
    ) {
    }

    public function create(array $data): int
    {
        $id = $this->faqs->create($this->preparePayload($data));
        AuditLog::record('faq.created', 'faq', $id);

        return $id;
    }

    public function update(int $id, array $data): void
    {
        $this->faqs->update($id, $this->preparePayload($data));
        AuditLog::record('faq.updated', 'faq', $id);
    }

    public function delete(int $id): void
    {
        $this->faqs->delete($id);
        AuditLog::record('faq.deleted', 'faq', $id);
    }

    private function preparePayload(array $data): array
    {
        return [
            'question'   => $data['question'],
            'answer'     => $data['answer'],
            'category'   => $data['category'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active'  => !empty($data['is_active']) ? 1 : 0,
        ];
    }
}
