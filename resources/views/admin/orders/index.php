<?php
$adminPath = \App\Core\Config::get('admin.path', '/admin');
$statuses = ['' => 'All', 'submitted' => 'Submitted', 'under_review' => 'Under Review', 'quoted' => 'Quoted', 'accepted' => 'Accepted', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'declined' => 'Declined', 'cancelled' => 'Cancelled'];
ob_start();
?>
<h1><i class="fa-solid fa-receipt"></i> Orders</h1>

<p style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;">
    <?php foreach ($statuses as $value => $label): ?>
        <a href="<?= e($adminPath) ?>/orders<?= $value ? '?status=' . e($value) : '' ?>"
           class="btn <?= $currentStatus === $value ? 'btn-primary' : 'btn-secondary' ?>" style="padding:6px 14px;font-size:0.8rem;min-height:auto;">
            <?= e($label) ?>
        </a>
    <?php endforeach; ?>
</p>

<table class="table">
    <thead><tr><th>Order #</th><th>Customer</th><th>Service</th><th>Status</th><th>Requested</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
            <td><?= e($o['order_number']) ?></td>
            <td><?= e($o['customer_name']) ?><br><span style="color:#94a3b8;font-size:0.8rem;"><?= e($o['customer_email']) ?></span></td>
            <td><?= e($o['service_name']) ?></td>
            <td><span class="badge badge-active"><?= e(str_replace('_', ' ', $o['status'])) ?></span></td>
            <td><?= e($o['created_at']) ?></td>
            <td><a href="<?= e($adminPath) ?>/orders/<?= e((string) $o['id']) ?>">View</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if (empty($orders)): ?>
    <p style="color:#94a3b8;margin-top:16px;">No orders here yet.</p>
<?php endif; ?>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
