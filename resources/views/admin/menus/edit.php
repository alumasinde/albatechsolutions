<?php ob_start(); $adminPath = \App\Core\Config::get('admin.path', '/admin'); ?>
<h1><i class="fa-solid fa-bars"></i> <?= e($menu['name']) ?></h1>

<p style="display:flex;gap:10px;">
    <a href="<?= e($adminPath) ?>/menus/header" class="btn <?= $menu['slug'] === 'header' ? 'btn-primary' : 'btn-secondary' ?>">Header</a>
    <a href="<?= e($adminPath) ?>/menus/footer" class="btn <?= $menu['slug'] === 'footer' ? 'btn-primary' : 'btn-secondary' ?>">Footer</a>
</p>

<table class="table" style="margin-bottom:24px;">
    <thead><tr><th>Label</th><th>URL</th><th>Order</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= e($item['label']) ?></td>
            <td><?= e($item['url']) ?></td>
            <td><?= (int) $item['sort_order'] ?></td>
            <td>
                <form method="POST" action="<?= e($adminPath . '/menu-items/' . $item['id'] . '/delete') ?>"
                      onsubmit="return confirm('Remove this menu item?');" style="display:inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-secondary btn-sm">Remove</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="card" style="max-width:480px;">
    <h2 style="margin-top:0;">Add Menu Item</h2>
    <form method="POST" action="<?= e($adminPath . '/menus/' . $menu['slug'] . '/items') ?>">
        <?= csrf_field() ?>
        <label for="label">Label</label>
        <input type="text" id="label" name="label" required>

        <label for="url">URL</label>
        <input type="text" id="url" name="url" placeholder="/about-us or https://..." required>

        <label for="sort_order">Sort Order</label>
        <input type="number" id="sort_order" name="sort_order" value="0">

        <button type="submit" class="btn btn-primary btn-block">Add Item</button>
    </form>
</div>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
