<?php
$adminPath = \App\Core\Config::get('admin.path', '/admin');
$isEdit = $post !== null;
ob_start();
?>
<h1><i class="fa-solid fa-newspaper"></i> <?= $isEdit ? 'Edit Post' : 'New Post' ?></h1>

<?php $errors = flash_errors(); ?>
<?php if ($errors): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $fieldErrors): foreach ($fieldErrors as $msg): ?>
            <p><?= e($msg) ?></p>
        <?php endforeach; endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= $isEdit ? e($adminPath . '/blog/' . $post['id']) : e($adminPath . '/blog') ?>" class="card">
    <?= csrf_field() ?>

    <label for="title">Title</label>
    <input type="text" id="title" name="title" value="<?= e($post['title'] ?? '') ?>" required>

    <label for="slug">URL Slug (leave blank to auto-generate)</label>
    <input type="text" id="slug" name="slug" value="<?= e($post['slug'] ?? '') ?>">

    <label for="category_id">Category</label>
    <select id="category_id" name="category_id">
        <option value="">— None —</option>
        <?php foreach ($categories as $c): ?>
            <option value="<?= e((string) $c['id']) ?>" <?= (($post['category_id'] ?? null) == $c['id']) ? 'selected' : '' ?>>
                <?= e($c['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="excerpt">Excerpt</label>
    <input type="text" id="excerpt" name="excerpt" value="<?= e($post['excerpt'] ?? '') ?>">

    <label>Content</label>
    <div id="editor" style="background:#fff;min-height:250px;border:1px solid #e2e8f0;border-radius:8px;"><?= $post['content'] ?? '' ?></div>
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
<form method="POST" action="<?= e($adminPath . '/blog/' . $post['id'] . '/delete') ?>"
      onsubmit="return confirm('Delete this post permanently?');" style="margin-top:12px;">
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
