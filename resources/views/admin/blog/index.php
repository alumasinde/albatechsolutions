<?php ob_start(); $adminPath = \App\Core\Config::get('admin.path', '/admin'); ?>
<h1><i class="fa-solid fa-newspaper"></i> Blog</h1>

<p><a href="<?= e($adminPath) ?>/blog/create" class="btn btn-primary"><i class="fa-solid fa-plus"></i> New Post</a></p>

<details class="card" style="margin-bottom:20px;">
    <summary style="cursor:pointer;font-weight:600;">Add category</summary>
    <form method="POST" action="<?= e($adminPath) ?>/blog-categories" style="margin-top:12px;">
        <?= csrf_field() ?>
        <input type="text" name="name" placeholder="Category name" required>
        <button type="submit" class="btn btn-secondary" style="margin-top:10px;">Add</button>
    </form>
</details>

<table class="table">
    <thead><tr><th>Image</th><th>Title</th><th>Category</th><th>Status</th><th>Updated</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($posts as $p): ?>
        <tr>
            <td>
                <?php if (!empty($p['featured_media_path'])): ?>
                    <img src="<?= e(url('/' . ltrim($p['featured_media_path'], '/'))) ?>" alt="" loading="lazy" style="width:72px;height:48px;object-fit:cover;border-radius:6px;display:block;">
                <?php else: ?>—<?php endif; ?>
            </td>
            <td><?= e($p['title']) ?></td>
            <td><?= e($p['category_name'] ?? '—') ?></td>
            <td><span class="badge <?= $p['status'] === 'published' ? 'badge-active' : 'badge-inactive' ?>"><?= e($p['status']) ?></span></td>
            <td><?= e($p['updated_at']) ?></td>
            <td><a href="<?= e($adminPath) ?>/blog/<?= e((string) $p['id']) ?>/edit">Edit</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
