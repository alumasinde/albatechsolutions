<?php

declare(strict_types=1);

namespace App\Modules\Cms\Service;

use App\Core\AuditLog;
use App\Core\BaseService;
use App\Core\Helpers\Sanitizer;
use App\Modules\Cms\Repository\PageRepository;

final class PageService extends BaseService
{
    public function __construct(
        private readonly PageRepository $pages
    ) {
    }

    public function create(array $data, int $authorId): int
    {
        $slug = $this->uniqueSlug($data['slug'] ?? $data['title']);

        $id = $this->pages->create($this->preparePayload($data, $slug, $authorId));
        AuditLog::record('page.created', 'page', $id);

        return $id;
    }

    public function update(int $id, array $data): void
    {
        $existing = $this->pages->find($id);
        $slug = $existing && $existing['slug'] === ($data['slug'] ?? null)
            ? $data['slug']
            : $this->uniqueSlug($data['slug'] ?? $data['title'], $id);

        $this->pages->update($id, [
            'title'             => $data['title'],
            'slug'              => $slug,
            'page_type'        => $this->pageType($data['page_type'] ?? 'general'),
            'content'           => Sanitizer::cleanRichText($data['content'] ?? ''),
            'excerpt'           => $data['excerpt'] ?? null,
            'status'            => in_array($data['status'] ?? 'draft', ['draft', 'published'], true) ? $data['status'] : 'draft',
            'meta_title'        => $data['meta_title'] ?? null,
            'meta_description'  => $data['meta_description'] ?? null,
            'meta_keywords'     => $data['meta_keywords'] ?? null,
            'canonical_url'     => $data['canonical_url'] ?? null,
            'focus_keyword'    => $data['focus_keyword'] ?? null,
            'seo_intro'        => $data['seo_intro'] ?? null,
            'noindex'         => !empty($data['noindex']) ? 1 : 0,
            'published_at'      => ($data['status'] ?? 'draft') === 'published' ? ($existing['published_at'] ?? date('Y-m-d H:i:s')) : null,
        ]);

        AuditLog::record('page.updated', 'page', $id);
    }

    public function delete(int $id): void
    {
        $this->pages->delete($id);
        AuditLog::record('page.deleted', 'page', $id);
    }

    private function preparePayload(array $data, string $slug, int $authorId): array
    {
        $status = in_array($data['status'] ?? 'draft', ['draft', 'published'], true) ? $data['status'] : 'draft';

        return [
            'title'            => $data['title'],
            'slug'             => $slug,
            'page_type'       => $this->pageType($data['page_type'] ?? 'general'),
            'content'          => Sanitizer::cleanRichText($data['content'] ?? ''),
            'excerpt'          => $data['excerpt'] ?? null,
            'focus_keyword'   => $data['focus_keyword'] ?? null,
            'seo_intro'       => $data['seo_intro'] ?? null,
            'noindex'         => !empty($data['noindex']) ? 1 : 0,
            'status'           => $status,
            'meta_title'       => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords'    => $data['meta_keywords'] ?? null,
            'canonical_url'    => $data['canonical_url'] ?? null,
            'author_id'        => $authorId,
            'published_at'     => $status === 'published' ? date('Y-m-d H:i:s') : null,
        ];
    }


    private function pageType(string $type): string
    {
        return in_array($type, ['general', 'service_intent', 'industry', 'location'], true) ? $type : 'general';
    }

    private function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Sanitizer::slug($source);
        $slug = $base;
        $suffix = 1;

        while (true) {
            $existing = $this->pages->findAnyBySlug($slug);
            if (!$existing || ($ignoreId !== null && (int) $existing['id'] === $ignoreId)) {
                return $slug;
            }
            $slug = $base . '-' . (++$suffix);
        }
    }
}
