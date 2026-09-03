<?php
$adminPath = \App\Core\Config::get('admin.path', '/admin');
$isEdit = $page !== null;
ob_start();
?>
<h1><i class="fa-solid fa-file-lines"></i> <?= $isEdit ? 'Edit Page' : 'New Page' ?></h1>

<?php $errors = flash_errors(); ?>
<?php if ($errors): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $fieldErrors): foreach ($fieldErrors as $msg): ?>
            <p><?= e($msg) ?></p>
        <?php endforeach; endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= $isEdit ? e($adminPath . '/pages/' . $page['id']) : e($adminPath . '/pages') ?>" class="card">
    <?= csrf_field() ?>

    <label for="title">Title</label>
    <input type="text" id="title" name="title" value="<?= e($page['title'] ?? '') ?>" required>

    <label for="slug">URL Slug (leave blank to auto-generate)</label>
    <input type="text" id="slug" name="slug" value="<?= e($page['slug'] ?? '') ?>" placeholder="e.g. about-us">

    <label for="excerpt">Excerpt</label>
    <input type="text" id="excerpt" name="excerpt" value="<?= e($page['excerpt'] ?? '') ?>">

    <label for="page_type">Page Type</label>
    <select id="page_type" name="page_type">
        <?php foreach (['general' => 'General page', 'service_intent' => 'SEO service / search intent', 'industry' => 'Industry landing page', 'location' => 'Location landing page'] as $value => $label): ?>
            <option value="<?= e($value) ?>" <?= ($page['page_type'] ?? 'general') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="focus_keyword">Primary Search Topic</label>
    <input type="text" id="focus_keyword" name="focus_keyword" value="<?= e($page['focus_keyword'] ?? '') ?>" placeholder="e.g. web development company in Kenya">

    <label for="seo_intro">SEO Introduction</label>
    <textarea id="seo_intro" name="seo_intro" rows="3" maxlength="700" placeholder="A concise introduction for the landing page."><?= e($page['seo_intro'] ?? '') ?></textarea>

    <label>Content</label>
    <div id="editor" style="background:#fff;min-height:250px;border:1px solid #e2e8f0;border-radius:8px;"><?= $page['content'] ?? '' ?></div>
    <textarea name="content" id="content-input" style="display:none;"></textarea>

    <div class="form-section">
        <h2>SEO</h2>
        <label for="meta_title">Meta Title</label>
        <input type="text" id="meta_title" name="meta_title" value="<?= e($page['meta_title'] ?? '') ?>">

        <label for="meta_description">Meta Description</label>
        <input type="text" id="meta_description" name="meta_description" value="<?= e($page['meta_description'] ?? '') ?>">

        <label for="meta_keywords">Meta Keywords</label>
        <input type="text" id="meta_keywords" name="meta_keywords" value="<?= e($page['meta_keywords'] ?? '') ?>">

        <label for="canonical_url">Canonical URL</label>
        <input type="text" id="canonical_url" name="canonical_url" value="<?= e($page['canonical_url'] ?? '') ?>">

        <label class="check-row" for="noindex"><input type="checkbox" id="noindex" name="noindex" value="1" <?= !empty($page['noindex']) ? 'checked' : '' ?>> Keep this page out of search indexes</label>
    </div>

    <label for="status">Status</label>
    <select id="status" name="status">
        <option value="draft" <?= ($page['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="published" <?= ($page['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
    </select>

    <button type="submit" class="btn btn-primary" style="margin-top:20px;">Save Page</button>
</form>

<?php if ($isEdit): ?>
<form method="POST" action="<?= e($adminPath . '/pages/' . $page['id'] . '/delete') ?>"
      onsubmit="return confirm('Delete this page permanently?');" style="margin-top:12px;">
    <?= csrf_field() ?>
    <button type="submit" class="btn btn-danger">Delete Page</button>
</form>
<?php endif; ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>
<script src="<?= asset('js/v4/rich-editor.js') ?>"></script>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
