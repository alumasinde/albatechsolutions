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
        <h1><i class="fa-solid fa-receipt"></i> Order <?= e($order['order_number']) ?></h1>
        <a href="/orders" class="btn btn-secondary">Back to My Orders</a>
    </header>

    <?php $success = \App\Core\Session::getFlash('_success'); ?>
    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

    <?php $errors = flash_errors(); ?>
    <?php if ($errors): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $fieldErrors): foreach ($fieldErrors as $msg): ?>
                <p><?= e($msg) ?></p>
            <?php endforeach; endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="card" style="margin-bottom:20px;">
        <p><strong>Service:</strong> <?= e($order['service_name']) ?></p>
        <p><strong>Status:</strong> <span class="badge badge-active"><?= e($statusLabels[$order['status']] ?? $order['status']) ?></span></p>
        <p><strong>Your request:</strong><br><?= nl2br(e($order['customer_notes'])) ?></p>

        <?php if ($order['status'] === 'quoted'): ?>
            <div class="alert alert-warning">
                <p style="margin:0 0 10px;">Quote: <strong>KES <?= number_format((float) $order['quoted_price'], 2) ?></strong></p>
                <form method="POST" action="/orders/<?= e((string) $order['id']) ?>/accept" style="display:inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary btn-sm">Accept Quote</button>
                </form>
                <form method="POST" action="/orders/<?= e((string) $order['id']) ?>/decline" style="display:inline;"
                      onsubmit="return confirm('Decline this quote?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-secondary btn-sm">Decline</button>
                </form>
            </div>
        <?php elseif (in_array($order['status'], ['accepted', 'in_progress', 'completed'], true) && $order['quoted_price']): ?>
            <p><strong>Agreed price:</strong> KES <?= number_format((float) ($order['agreed_price'] ?: $order['quoted_price']), 2) ?>
                <?= $order['paid_at'] ? ' — Paid' : ' — Payment pending' ?>
            </p>
            <?php if ($order['status'] === 'accepted' && !$order['paid_at']): ?>
                <a href="/orders/<?= e((string) $order['id']) ?>/pay" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-money-bill-wave"></i> Pay Now
                </a>
            <?php elseif ($order['paid_at']): ?>
                <a href="/orders/<?= e((string) $order['id']) ?>/receipt" target="_blank" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-file-pdf"></i> Download Receipt
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="card" style="margin-bottom:20px;">
        <h2 style="margin-top:0;">Timeline</h2>
        <ul class="activity-list">
            <?php foreach ($history as $h): ?>
                <li>
                    <span class="activity-action"><?= e(str_replace('_', ' ', $h['status'])) ?></span>
                    <span class="activity-meta"><?= e($h['created_at']) ?><?= $h['note'] ? ' — ' . e($h['note']) : '' ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="card">
        <h2 style="margin-top:0;">Documents</h2>
        <?php foreach ($documents as $doc): ?>
            <p><a href="/orders/<?= e((string) $order['id']) ?>/documents/<?= e((string) $doc['id']) ?>/download">
                <i class="fa-solid fa-file"></i> <?= e($doc['original_name']) ?>
            </a></p>
        <?php endforeach; ?>
        <?php if (empty($documents)): ?><p style="color:#94a3b8;">No documents uploaded yet.</p><?php endif; ?>

        <form method="POST" action="/orders/<?= e((string) $order['id']) ?>/documents" enctype="multipart/form-data" style="margin-top:16px;">
            <?= csrf_field() ?>
            <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
            <button type="submit" class="btn btn-secondary" style="margin-top:10px;">Upload Document</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layouts/app.php';
