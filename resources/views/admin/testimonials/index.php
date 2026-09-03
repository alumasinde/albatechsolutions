<?php ob_start(); $adminPath = \App\Core\Config::get('admin.path', '/admin'); ?>
<h1><i class="fa-solid fa-quote-left"></i> Testimonials</h1>

<div class="card" style="margin-bottom:24px;">
    <h2 style="margin-top:0;">Add Testimonial</h2>
    <form method="POST" action="<?= e($adminPath) ?>/testimonials" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <label for="client_name">Client Name</label>
        <input type="text" id="client_name" name="client_name" required>

        <label for="client_title">Client Title</label>
        <input type="text" id="client_title" name="client_title">

        <label for="client_company">Company</label>
        <input type="text" id="client_company" name="client_company">

        <label for="photo">Photo</label>
        <input type="file" id="photo" name="photo" accept="image/*">

        <label for="quote">Quote</label>
        <textarea id="quote" name="quote" rows="3" required></textarea>

        <label for="rating">Rating (1-5, optional)</label>
        <input type="number" id="rating" name="rating" min="1" max="5">

        <div class="checkbox-row">
            <input type="checkbox" id="is_active" name="is_active" value="1" checked>
            <label for="is_active" style="margin:0;font-weight:400;">Active</label>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Add Testimonial</button>
    </form>
</div>

<table class="table">
    <thead><tr><th>Client</th><th>Company</th><th>Status</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($testimonials as $t): ?>
        <tr>
            <td><?= e($t['client_name']) ?></td>
            <td><?= e($t['client_company'] ?? '—') ?></td>
            <td><span class="badge <?= $t['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $t['is_active'] ? 'Active' : 'Inactive' ?></span></td>
            <td>
                <form method="POST" action="<?= e($adminPath . '/testimonials/' . $t['id'] . '/delete') ?>"
                      onsubmit="return confirm('Delete this testimonial?');" style="display:inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-secondary btn-sm">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
