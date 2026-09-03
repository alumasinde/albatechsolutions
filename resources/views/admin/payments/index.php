<?php
ob_start();
$adminPath = \App\Core\Config::get('admin.path', '/admin');
$statuses = ['' => 'All', 'pending' => 'Pending', 'completed' => 'Completed', 'failed' => 'Failed', 'rejected' => 'Rejected'];
$statusBadge = [
    'completed' => 'background:#dcfce7;color:#166534;',
    'pending'   => 'background:#fef3c7;color:#92400e;',
    'failed'    => 'background:#fee2e2;color:#991b1b;',
    'rejected'  => 'background:#fee2e2;color:#991b1b;',
];
?>
<h1><i class="fa-solid fa-money-bill-wave"></i> Payments</h1>

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

<h2 style="font-size:1.05rem;margin:24px 0 10px;">Bank Transfers Awaiting Verification</h2>
<table class="table">
    <thead><tr><th>Order #</th><th>Customer</th><th>Amount</th><th>Gateway</th><th>Submitted</th><th>Proof</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($pending as $p): ?>
        <tr>
            <td><?= e($p['order_number']) ?></td>
            <td><?= e($p['customer_name']) ?></td>
            <td><?= e($p['currency'] ?? 'KES') ?> <?= number_format((float) $p['amount'], 2) ?></td>
            <td><?= e($p['gateway'] ?? 'manual') ?></td>
            <td><?= e($p['created_at']) ?></td>
            <td><a href="<?= e($adminPath) ?>/payments/<?= e((string) $p['id']) ?>/proof"><i class="fa-solid fa-file"></i> View</a></td>
            <td style="white-space:nowrap;">
                <form method="POST" action="<?= e($adminPath . '/payments/' . $p['id'] . '/verify') ?>" style="display:inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary btn-sm">Verify</button>
                </form>
                <form method="POST" action="<?= e($adminPath . '/payments/' . $p['id'] . '/reject') ?>" style="display:inline;"
                      onsubmit="return confirm('Reject this payment proof?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-secondary btn-sm">Reject</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php if (empty($pending)): ?>
    <p style="color:#94a3b8;margin:10px 0 0;">No bank transfers awaiting verification.</p>
<?php endif; ?>

<h2 style="font-size:1.05rem;margin:32px 0 10px;">All Payments</h2>
<p style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
    <?php foreach ($statuses as $value => $label): ?>
        <a href="<?= e($adminPath) ?>/payments<?= $value ? '?status=' . e($value) : '' ?>"
           class="btn <?= $currentStatus === $value ? 'btn-primary' : 'btn-secondary' ?>" style="padding:6px 14px;font-size:0.8rem;min-height:auto;">
            <?= e($label) ?>
        </a>
    <?php endforeach; ?>
</p>

<table class="table">
    <thead><tr><th>Date</th><th>Order #</th><th>Customer</th><th>Amount</th><th>Method</th><th>Status</th><th>Reference</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($payments as $p): ?>
        <tr>
            <td><?= e($p['created_at']) ?></td>
            <td><?= $p['order_number'] ? e($p['order_number']) : '—' ?></td>
            <td><?= $p['customer_name'] ? e($p['customer_name']) : '—' ?></td>
            <td><?= e($p['currency'] ?? 'KES') ?> <?= number_format((float) $p['amount'], 2) ?></td>
            <td><?= e($p['gateway'] ?? $p['method'] ?? '—') ?></td>
            <td><span class="badge" style="<?= $statusBadge[$p['status']] ?? '' ?>"><?= e(ucfirst((string) $p['status'])) ?></span></td>
            <td style="font-size:0.8rem;color:#64748b;"><?= e($p['reference'] ?? '') ?></td>
            <td>
                <?php if ($p['status'] === 'completed' && $p['context_type'] === 'order'): ?>
                    <a href="<?= e($adminPath) ?>/payments/<?= e((string) $p['id']) ?>/receipt" target="_blank">
                        <i class="fa-solid fa-file-pdf"></i> Receipt
                    </a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php if (empty($payments)): ?>
    <p style="color:#94a3b8;margin-top:16px;">No payments found<?= $currentStatus ? ' for this status' : '' ?>.</p>
<?php endif; ?>

<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
