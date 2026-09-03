<?php
$adminPath = \App\Core\Config::get('admin.path', '/admin');
$forwardMap = [
    'submitted' => 'under_review',
    'under_review' => null, // quoting handles this transition instead
    'accepted' => 'in_progress',
    'in_progress' => 'completed',
];
ob_start();
?>
<h1><i class="fa-solid fa-receipt"></i> Order <?= e($order['order_number']) ?></h1>

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
    <p><strong>Customer:</strong> <?= e($order['customer_name']) ?> — <?= e($order['customer_email']) ?><?= $order['customer_phone'] ? ' — ' . e($order['customer_phone']) : '' ?></p>
    <p><strong>Service:</strong> <?= e($order['service_name']) ?></p>
    <p><strong>Status:</strong> <span class="badge badge-active"><?= e(str_replace('_', ' ', $order['status'])) ?></span></p>
    <p><strong>Customer's request:</strong><br><?= nl2br(e($order['customer_notes'])) ?></p>
    <?php if ($order['quoted_price']): ?>
        <p><strong>Quoted price:</strong> KES <?= number_format((float) $order['quoted_price'], 2) ?></p>
    <?php endif; ?>
    <?php if ($order['paid_at']): ?>
        <p><strong>Paid:</strong> <?= e($order['paid_at']) ?>
            &nbsp;<a href="/orders/<?= e((string) $order['id']) ?>/receipt" target="_blank"><i class="fa-solid fa-file-pdf"></i> View Receipt</a>
        </p>
    <?php endif; ?>
</div>

<div class="card" style="margin-bottom:20px;">
    <h2 style="margin-top:0;">Actions</h2>

    <?php if ($order['status'] === 'under_review'): ?>
        <form method="POST" action="<?= e($adminPath . '/orders/' . $order['id'] . '/quote') ?>" style="margin-bottom:20px;">
            <?= csrf_field() ?>
            <label for="quoted_price">Quote Price (KES)</label>
            <input type="number" id="quoted_price" name="quoted_price" step="0.01" required>
            <label for="note">Note to customer (optional)</label>
            <input type="text" id="note" name="note">
            <button type="submit" class="btn btn-primary" style="margin-top:10px;">Send Quote</button>
        </form>
    <?php endif; ?>

    <?php if (($forwardMap[$order['status']] ?? null)): ?>
        <form method="POST" action="<?= e($adminPath . '/orders/' . $order['id'] . '/status') ?>" style="display:inline-block;margin-right:10px;">
            <?= csrf_field() ?>
            <input type="hidden" name="status" value="<?= e($forwardMap[$order['status']]) ?>">
            <button type="submit" class="btn btn-primary">
                Move to <?= e(str_replace('_', ' ', $forwardMap[$order['status']])) ?>
            </button>
        </form>
    <?php endif; ?>

    <?php if ($order['status'] === 'accepted' && !$order['paid_at']): ?>
        <form method="POST" action="<?= e($adminPath . '/orders/' . $order['id'] . '/mark-paid') ?>" style="display:inline-block;margin-right:10px;">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-primary">Mark as Paid</button>
        </form>
    <?php endif; ?>

    <?php if (!in_array($order['status'], ['completed', 'declined', 'cancelled', 'in_progress'], true)): ?>
        <form method="POST" action="<?= e($adminPath . '/orders/' . $order['id'] . '/status') ?>" style="display:inline-block;"
              onsubmit="return confirm('Cancel this order?');">
            <?= csrf_field() ?>
            <input type="hidden" name="status" value="cancelled">
            <button type="submit" class="btn btn-danger">Cancel Order</button>
        </form>
    <?php endif; ?>

    <?php if ($order['status'] === 'in_progress' && $order['paid_at'] && \App\Core\Auth::can('payments.refund')): ?>
        <details style="display:inline-block;vertical-align:top;">
            <summary class="btn btn-danger" style="cursor:pointer;list-style:none;">Cancel &amp; Refund</summary>
            <form method="POST" action="<?= e($adminPath . '/orders/' . $order['id'] . '/refund') ?>"
                  style="margin-top:10px;padding:14px;border:1px solid #e2e8f0;border-radius:8px;max-width:420px;"
                  onsubmit="return confirm('This cancels the order. If \'process via Paystack\' is checked, it also triggers a real refund. Continue?');">
                <?= csrf_field() ?>
                <label for="refund_reason">Reason</label>
                <textarea id="refund_reason" name="reason" rows="2" required placeholder="Why is this order being cancelled and refunded?"></textarea>
                <label class="check-row" style="margin-top:8px;">
                    <input type="checkbox" name="process_via_gateway" value="1" checked>
                    Process refund via Paystack now (uncheck if you already refunded manually)
                </label>
                <button type="submit" class="btn btn-danger" style="margin-top:10px;">Confirm Cancel &amp; Refund</button>
            </form>
        </details>
    <?php endif; ?>
</div>

<div class="card" style="margin-bottom:20px;">
    <h2 style="margin-top:0;">Timeline</h2>
    <ul class="activity-list">
        <?php foreach ($history as $h): ?>
            <li>
                <span class="activity-action"><?= e(str_replace('_', ' ', $h['status'])) ?></span>
                <span class="activity-meta"><?= e($h['changed_by_name'] ?? 'System') ?> · <?= e($h['created_at']) ?><?= $h['note'] ? ' — ' . e($h['note']) : '' ?></span>
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
    <?php if (empty($documents)): ?><p style="color:#94a3b8;">No documents uploaded.</p><?php endif; ?>
</div>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
