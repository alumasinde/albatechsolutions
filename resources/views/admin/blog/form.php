<?php
$adminPath = \App\Core\Config::get('admin.path', '/admin');
$isEdit = $post !== null;
$selectedMediaId = (int) ($post['featured_media_id'] ?? 0);
$selectedMedia = null;
foreach (($media ?? []) as $item) {
    if ((int) $item['id'] === $selectedMediaId) {
        $selectedMedia = $item;
        break;
    }
}
ob_start();
?>
<style>
.blog-media-field{margin:24px 0}.blog-media-field__header{display:flex;justify-content:space-between;align-items:flex-start;gap:16px}.form-help{margin:0 0 10px;color:#64748b;font-size:.82rem}.blog-media-preview{display:flex;align-items:center;gap:12px;margin-top:12px;padding:10px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc}.blog-media-preview.is-empty{color:#64748b}.blog-media-preview img{width:180px;height:100px;object-fit:cover;border-radius:6px}.blog-media-library{margin-top:18px;padding-top:18px;border-top:1px solid #e2e8f0}.blog-media-library__heading{display:flex;justify-content:space-between;gap:12px;margin-bottom:10px;font-size:.85rem}.blog-media-library__heading span{color:#64748b}.blog-media-library__grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:10px}.blog-media-item{padding:3px;border:2px solid transparent;border-radius:8px;background:#fff;cursor:pointer}.blog-media-item:hover,.blog-media-item.is-selected{border-color:var(--color-primary,#2563eb)}.blog-media-item img{display:block;width:100%;height:72px;object-fit:cover;border-radius:5px}.ql-editor img{max-width:100%;height:auto}.ql-image[disabled]{opacity:.5;cursor:wait}@media(max-width:600px){.blog-media-field__header{flex-direction:column}.blog-media-preview{align-items:flex-start;flex-direction:column}.blog-media-preview img{width:100%;height:auto;max-height:240px}}
</style>
<h1><i class="fa-solid fa-newspaper"></i> <?= $isEdit ? 'Edit Post' : 'New Post' ?></h1>

<?php $errors = flash_errors(); ?>
<?php if ($errors): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $fieldErrors): foreach ($fieldErrors as $msg): ?>
            <p><?= e($msg) ?></p>
        <?php endforeach; endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= $isEdit ? e($adminPath . '/blog/' . $post['id']) : e($adminPath . '/blog') ?>" class="card" enctype="multipart/form-data" id="blog-post-form">
    <?= csrf_field() ?>

    <label for="title">Title</label>
    <input type="text" id="title" name="title" value="<?= e($post['title'] ?? '') ?>" required>

    <label for="slug">URL Slug (leave blank to auto-generate)</label>
    <input type="text" id="slug" name="slug" value="<?= e($post['slug'] ?? '') ?>">

    <label for="category_id">Category</label>
    <select id="category_id" name="category_id">
        <option value="">— None —</option>
        <?php foreach ($categories as $c): ?>
            <option value="<?= e((string) $c['id']) ?>" <?= (($post['category_id'] ?? null) == $c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <section class="blog-media-field" aria-labelledby="featured-image-heading">
        <div class="blog-media-field__header">
            <div>
                <label id="featured-image-heading" for="featured_image">Featured Image</label>
                <p class="form-help">Shown on guide cards, the article page and social previews. JPG, PNG, WebP or SVG, up to 3MB.</p>
            </div>
            <?php if ($selectedMedia): ?><button type="button" class="btn btn-secondary btn-sm" id="remove-featured-image">Remove</button><?php endif; ?>
        </div>

        <input type="hidden" name="featured_media_id" id="featured_media_id" value="<?= $selectedMediaId ?: '' ?>">
        <input type="file" id="featured_image" name="featured_image" accept="image/jpeg,image/png,image/webp,image/svg+xml">

        <div id="featured-image-preview" class="blog-media-preview<?= $selectedMedia ? '' : ' is-empty' ?>">
            <?php if ($selectedMedia): ?>
                <img src="<?= e(url('/' . ltrim($selectedMedia['disk_path'], '/'))) ?>" alt="<?= e($selectedMedia['original_name']) ?>">
                <span><?= e($selectedMedia['original_name']) ?></span>
            <?php else: ?><span>No featured image selected</span><?php endif; ?>
        </div>

        <?php if (!empty($media)): ?>
            <div class="blog-media-library">
                <div class="blog-media-library__heading"><strong>Choose from Media Library</strong><span><?= count($media) ?> images available</span></div>
                <div class="blog-media-library__grid">
                    <?php foreach ($media as $item): ?>
                        <button type="button" class="blog-media-item<?= ((int) $item['id'] === $selectedMediaId) ? ' is-selected' : '' ?>" data-media-id="<?= e((string) $item['id']) ?>" data-media-url="<?= e(url('/' . ltrim($item['disk_path'], '/'))) ?>" data-media-name="<?= e($item['original_name']) ?>" aria-label="Use <?= e($item['original_name']) ?> as featured image">
                            <img src="<?= e(url('/' . ltrim($item['disk_path'], '/'))) ?>" alt="" loading="lazy">
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <label for="excerpt">Excerpt</label>
    <input type="text" id="excerpt" name="excerpt" value="<?= e($post['excerpt'] ?? '') ?>">

    <label>Content</label>
    <div id="editor" data-image-upload-url="<?= e($adminPath . '/blog/media') ?>" style="background:#fff;min-height:250px;border:1px solid #e2e8f0;border-radius:8px;"><?= $post['content'] ?? '' ?></div>
    <textarea name="content" id="content-input" style="display:none;"></textarea>

    <div class="form-section">
        <h2>SEO</h2>
        <label for="meta_title">Meta Title</label>
        <input type="text" id="meta_title" name="meta_title" value="<?= e($post['meta_title'] ?? '') ?>">
        <label for="meta_description">Meta Description</label>
        <input type="text" id="meta_description" name="meta_description" value="<?= e($post['meta_description'] ?? '') ?>">
    </div>

    <label for="status">Status</label>
    <select id="status" name="status">
        <option value="draft" <?= ($post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
    </select>

    <button type="submit" class="btn btn-primary" style="margin-top:20px;">Save Post</button>
</form>

<?php if ($isEdit): ?>
<form method="POST" action="<?= e($adminPath . '/blog/' . $post['id'] . '/delete') ?>" onsubmit="return confirm('Delete this post permanently?');" style="margin-top:12px;">
    <?= csrf_field() ?>
    <button type="submit" class="btn btn-danger">Delete Post</button>
</form>
<?php endif; ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>
<script src="<?= asset('js/v4/rich-editor.js') ?>"></script>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
