<?php ob_start(); ?>
<div class="page-wrapper">
    <header class="page-header">
        <h1><i class="fa-solid fa-paper-plane"></i> Request: <?= e($service['name']) ?></h1>
        <a href="/services/<?= e($service['slug']) ?>" class="btn btn-secondary">Back to service</a>
    </header>

    <?php $errors = flash_errors(); ?>
    <?php if ($errors): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $fieldErrors): foreach ($fieldErrors as $msg): ?>
                <p><?= e($msg) ?></p>
            <?php endforeach; endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/orders" enctype="multipart/form-data" class="card" style="max-width:560px;">
        <?= csrf_field() ?>
        <input type="hidden" name="service_slug" value="<?= e($service['slug']) ?>">

        <p style="color:#64748b;">
            <?= e($service['summary'] ?? '') ?>
            <?php if ($service['price_type'] !== 'quote'): ?>
                — <?= $service['price_type'] === 'starting_from' ? 'From ' : '' ?>KES <?= number_format((float) $service['price'], 0) ?>
            <?php else: ?>
                — Quote on request
            <?php endif; ?>
        </p>

        <label for="customer_notes">Tell us what you need</label>
        <textarea id="customer_notes" name="customer_notes" rows="5" required placeholder="e.g. I need a KRA PIN registered for a new business, ideally by Friday."></textarea>

        <label for="document">Attach a document (optional — PDF, JPG, or PNG, max 5MB)</label>
        <input type="file" id="document" name="document" accept=".pdf,.jpg,.jpeg,.png">

        <button type="submit" class="btn btn-primary btn-block">Submit Request</button>
    </form>
</div>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layouts/app.php';
