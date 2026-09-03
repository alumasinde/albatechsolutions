<?php

declare(strict_types=1);

namespace App\Modules\Cms\Service;

use App\Core\AuditLog;
use App\Core\BaseService;
use App\Core\Database;
use App\Core\Helpers\Sanitizer;
use App\Modules\Cms\Repository\BlogPostRepository;

final class BlogPostService extends BaseService
{
    public function __construct(
        private readonly BlogPostRepository $posts
    ) {
    }

    public function create(array $data, int $authorId): int
    {
        $slug = $this->uniqueSlug($data['slug'] ?? $data['title']);

        $id = $this->posts->create($this->preparePayload($data, $slug, $authorId));
        AuditLog::record('blog_post.created', 'blog_post', $id);

        return $id;
    }

    public function update(int $id, array $data): void
    {
        $existing = $this->posts->find($id);
        $slug = $existing && $existing['slug'] === ($data['slug'] ?? null)
            ? $data['slug']
            : $this->uniqueSlug($data['slug'] ?? $data['title'], $id);

        $status = in_array($data['status'] ?? 'draft', ['draft', 'published'], true) ? $data['status'] : 'draft';

        $this->posts->update($id, [
            'category_id'       => !empty($data['category_id']) ? $data['category_id'] : null,
            'title'             => $data['title'],
            'slug'              => $slug,
            'content'           => Sanitizer::cleanRichText($data['content'] ?? ''),
            'excerpt'           => $data['excerpt'] ?? null,
            'status'            => $status,
            'meta_title'        => $data['meta_title'] ?? null,
            'meta_description'  => $data['meta_description'] ?? null,
            'meta_keywords'     => $data['meta_keywords'] ?? null,
            'published_at'      => $status === 'published' ? ($existing['published_at'] ?? date('Y-m-d H:i:s')) : null,
        ]);

        AuditLog::record('blog_post.updated', 'blog_post', $id);
    }

    public function delete(int $id): void
    {
        $this->posts->delete($id);
        AuditLog::record('blog_post.deleted', 'blog_post', $id);
    }

    private function preparePayload(array $data, string $slug, int $authorId): array
    {
        $status = in_array($data['status'] ?? 'draft', ['draft', 'published'], true) ? $data['status'] : 'draft';

        return [
            'category_id'      => !empty($data['category_id']) ? $data['category_id'] : null,
            'title'            => $data['title'],
            'slug'             => $slug,
            'content'          => Sanitizer::cleanRichText($data['content'] ?? ''),
            'excerpt'          => $data['excerpt'] ?? null,
            'status'           => $status,
            'meta_title'       => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords'    => $data['meta_keywords'] ?? null,
            'author_id'        => $authorId,
            'published_at'     => $status === 'published' ? date('Y-m-d H:i:s') : null,
        ];
    }

    private function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Sanitizer::slug($source);
        $slug = $base;
        $suffix = 1;
        $db = Database::connection();

        while (true) {
            $stmt = $db->prepare('SELECT id FROM blog_posts WHERE slug = :slug AND deleted_at IS NULL LIMIT 1');
            $stmt->execute(['slug' => $slug]);
            $existing = $stmt->fetch();

            if (!$existing || ($ignoreId !== null && (int) $existing['id'] === $ignoreId)) {
                return $slug;
            }
            $slug = $base . '-' . (++$suffix);
        }
    }
}
