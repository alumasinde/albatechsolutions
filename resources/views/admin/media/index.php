<?php ob_start(); $adminPath = \App\Core\Config::get('admin.path', '/admin'); ?>
<h1><i class="fa-solid fa-photo-film"></i> Media Library</h1>

<div class="card" style="margin-bottom:24px;max-width:480px;">
    <form method="POST" action="<?= e($adminPath) ?>/media" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <label for="file">Upload File</label>
        <input type="file" id="file" name="file" accept="image/*" required>
        <input type="hidden" name="purpose" value="library">
        <button type="submit" class="btn btn-primary btn-block" style="margin-top:12px;">Upload</button>
    </form>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:16px;">
    <?php foreach ($media as $m): ?>
        <div class="card" style="padding:10px;">
            <img src="<?= e(url($m['disk_path'])) ?>" alt="<?= e($m['original_name']) ?>" style="width:100%;height:100px;object-fit:cover;border-radius:6px;" loading="lazy">
            <p style="font-size:0.75rem;color:#64748b;margin:8px 0 4px;word-break:break-all;"><?= e($m['original_name']) ?></p>
            <form method="POST" action="<?= e($adminPath . '/media/' . $m['id'] . '/delete') ?>"
                  onsubmit="return confirm('Delete this file?');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-secondary btn-sm" style="width:100%;">Delete</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
