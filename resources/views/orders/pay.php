<?php ob_start(); ?>
<div class="page-wrapper">
    <header class="page-header">
        <h1><i class="fa-solid fa-money-bill-wave"></i> Pay for Order <?= e($order['order_number']) ?></h1>
        <a href="/orders/<?= e((string) $order['id']) ?>" class="btn btn-secondary">Back to Order</a>
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

    <div class="pay-amount-card">
        <div>
            <p class="pay-amount-label">Amount due</p>
            <p class="pay-amount-value"><?= e($order['currency'] ?? 'KES') ?> <?= number_format((float) $order['quoted_price'], 2) ?></p>
        </div>
        <i class="fa-solid fa-file-invoice" style="font-size:1.6rem;color:#cbd5e1;"></i>
    </div>

    <?php if ($latestPayment && $latestPayment['status'] === 'pending'): ?>
        <div class="pay-pending-note">
            <i class="fa-solid fa-clock"></i>
            <div>
                <strong>Payment in progress.</strong> If you have already opened the checkout popup, complete the payment there before trying again.
                <span class="pay-pending-ref">Reference: <?= e($latestPayment['reference'] ?? '') ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid-3">
        <div class="card pay-card pay-card--primary">
            <span class="pay-card-badge">Recommended</span>
            <div class="pay-card-icon"><i class="fa-solid fa-bolt"></i></div>
            <h2>Pay online</h2>
            <p>
                Pay instantly with Paystack. Kenyan customers can use M-PESA, and other checkout channels are also offered by Paystack. No waiting for manual verification.
            </p>
            <button type="button" id="pay-online-btn"
                class="btn btn-primary btn-block"
                data-order-id="<?= e((string) $order['id']) ?>"
                data-csrf="<?= e(\App\Core\Helpers\Csrf::token()) ?>">
                <i class="fa-solid fa-lock"></i> Pay with Paystack
            </button>
            <div class="pay-trust-row">
                <i class="fa-solid fa-shield-halved"></i> Secured by Paystack — card details never touch our servers.
            </div>
        </div>

        <div class="card pay-card">
            <div class="pay-card-icon"><i class="fa-solid fa-building-columns"></i></div>
            <h2>Pay by Bank Transfer</h2>
            <p>
                Transfer <?= e($order['currency'] ?? 'KES') ?> <?= number_format((float) $order['quoted_price'], 2) ?> to our account, then upload proof below. Our team will verify it and mark your order as paid — this can take a few hours.
            </p>
            <?php if (setting('bank_account_details')): ?>
                <p class="pay-bank-details"><?= nl2br(e(setting('bank_account_details'))) ?></p>
            <?php endif; ?>
            <form method="POST" action="/orders/<?= e((string) $order['id']) ?>/pay/bank-transfer" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <label for="proof">Proof of Payment (PDF, JPG, or PNG)</label>
                <input type="file" id="proof" name="proof" accept=".pdf,.jpg,.jpeg,.png" required>
                <button type="submit" class="btn btn-secondary btn-block">Submit Proof</button>
            </form>
        </div>
    </div>
</div>

<script src="https://js.paystack.co/v2/inline.js"></script>
<script src="<?= asset('js/order-pay.js') ?>"></script>
<?php
$content = ob_get_clean();
$extraHead = '<link rel="preconnect" href="https://js.paystack.co"><link rel="preconnect" href="https://api.paystack.co"><link rel="preconnect" href="https://checkout.paystack.com" crossorigin>';
require dirname(__DIR__) . '/layouts/app.php';
