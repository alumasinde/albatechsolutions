<?php
$statusLabels = [
    'submitted' => 'Submitted', 'under_review' => 'Under Review', 'quoted' => 'Quote Ready',
    'accepted' => 'Accepted', 'in_progress' => 'In Progress', 'completed' => 'Completed',
    'declined' => 'Declined', 'cancelled' => 'Cancelled',
];
ob_start();
?>
<div class="page-wrapper">
    <header class="page-header">
        <h1><i class="fa-solid fa-receipt"></i> My Orders</h1>
        <a href="/services" class="btn btn-primary">Request a Service</a>
    </header>

    <table class="table">
        <thead><tr><th>Order #</th><th>Service</th><th>Status</th><th>Requested</th><th></th></tr></thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td><?= e($o['order_number']) ?></td>
                <td><?= e($o['service_name']) ?></td>
                <td><span class="badge <?= in_array($o['status'], ['completed', 'accepted', 'in_progress']) ? 'badge-active' : 'badge-inactive' ?>"><?= e($statusLabels[$o['status']] ?? $o['status']) ?></span></td>
                <td><?= e($o['created_at']) ?></td>
                <td><a href="/orders/<?= e((string) $o['id']) ?>">View</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (empty($orders)): ?>
        <p style="color:#94a3b8;margin-top:16px;">You haven't requested any services yet.</p>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layouts/app.php';
