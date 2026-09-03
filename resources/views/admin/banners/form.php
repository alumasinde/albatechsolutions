<?php
$adminPath = \App\Core\Config::get('admin.path', '/admin');
$isEdit = $banner !== null;
ob_start();
?>
<h1><i class="fa-solid fa-panorama"></i> <?= $isEdit ? 'Edit Banner' : 'New Banner' ?></h1>

<?php $errors = flash_errors(); ?>
<?php if ($errors): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $fieldErrors): foreach ($fieldErrors as $msg): ?>
            <p><?= e($msg) ?></p>
        <?php endforeach; endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data"
      action="<?= $isEdit ? e($adminPath . '/banners/' . $banner['id']) : e($adminPath . '/banners') ?>" class="card">
    <?= csrf_field() ?>

    <label for="title">Title</label>
    <input type="text" id="title" name="title" value="<?= e($banner['title'] ?? '') ?>">

    <label for="subtitle">Subtitle</label>
    <input type="text" id="subtitle" name="subtitle" value="<?= e($banner['subtitle'] ?? '') ?>">

    <label for="image">Image</label>
    <input type="file" id="image" name="image" accept="image/*">

    <label for="cta_label">Button Label</label>
    <input type="text" id="cta_label" name="cta_label" value="<?= e($banner['cta_label'] ?? '') ?>">

    <label for="cta_url">Button URL</label>
    <input type="text" id="cta_url" name="cta_url" value="<?= e($banner['cta_url'] ?? '') ?>">

    <label for="placement">Placement</label>
    <input type="text" id="placement" name="placement" value="<?= e($banner['placement'] ?? 'homepage_hero') ?>">

    <label for="sort_order">Sort Order</label>
    <input type="number" id="sort_order" name="sort_order" value="<?= e((string) ($banner['sort_order'] ?? 0)) ?>">

    <div class="form-section">
        <h2>Active Window (optional)</h2>
        <label for="starts_at">Starts At</label>
        <input type="datetime-local" id="starts_at" name="starts_at" value="<?= e($banner['starts_at'] ?? '') ?>">

        <label for="ends_at">Ends At</label>
        <input type="datetime-local" id="ends_at" name="ends_at" value="<?= e($banner['ends_at'] ?? '') ?>">
    </div>

    <div class="checkbox-row">
        <input type="checkbox" id="is_active" name="is_active" value="1" <?= ($banner['is_active'] ?? 1) ? 'checked' : '' ?>>
        <label for="is_active" style="margin:0;font-weight:400;">Active</label>
    </div>

    <button type="submit" class="btn btn-primary" style="margin-top:20px;">Save Banner</button>
</form>

<?php if ($isEdit): ?>
<form method="POST" action="<?= e($adminPath . '/banners/' . $banner['id'] . '/delete') ?>"
      onsubmit="return confirm('Delete this banner?');" style="margin-top:12px;">
    <?= csrf_field() ?>
    <button type="submit" class="btn btn-danger">Delete Banner</button>
</form>
<?php endif; ?>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
