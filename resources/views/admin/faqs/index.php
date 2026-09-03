<?php ob_start(); $adminPath = \App\Core\Config::get('admin.path', '/admin'); ?>
<h1><i class="fa-solid fa-circle-question"></i> FAQs</h1>

<div class="card" style="margin-bottom:24px;">
    <h2 style="margin-top:0;">Add FAQ</h2>
    <form method="POST" action="<?= e($adminPath) ?>/faqs">
        <?= csrf_field() ?>
        <label for="question">Question</label>
        <input type="text" id="question" name="question" required>

        <label for="answer">Answer</label>
        <textarea id="answer" name="answer" rows="3" required></textarea>

        <label for="category">Category (optional)</label>
        <input type="text" id="category" name="category">

        <label for="sort_order">Sort Order</label>
        <input type="number" id="sort_order" name="sort_order" value="0">

        <div class="checkbox-row">
            <input type="checkbox" id="is_active" name="is_active" value="1" checked>
            <label for="is_active" style="margin:0;font-weight:400;">Active</label>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Add FAQ</button>
    </form>
</div>

<table class="table">
    <thead><tr><th>Question</th><th>Category</th><th>Status</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($faqs as $f): ?>
        <tr>
            <td><?= e($f['question']) ?></td>
            <td><?= e($f['category'] ?? '—') ?></td>
            <td><span class="badge <?= $f['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $f['is_active'] ? 'Active' : 'Inactive' ?></span></td>
            <td>
                <form method="POST" action="<?= e($adminPath . '/faqs/' . $f['id'] . '/delete') ?>"
                      onsubmit="return confirm('Delete this FAQ?');" style="display:inline;">
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
