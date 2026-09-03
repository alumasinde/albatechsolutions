<?php
$adminPath = \App\Core\Config::get('admin.path', '/admin');
$isEdit = $service !== null;
ob_start();
?>
<h1><i class="fa-solid fa-briefcase"></i> <?= $isEdit ? 'Edit Service' : 'New Service' ?></h1>

<?php $errors = flash_errors(); ?>
<?php if ($errors): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $fieldErrors): foreach ($fieldErrors as $msg): ?>
            <p><?= e($msg) ?></p>
        <?php endforeach; endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= $isEdit ? e($adminPath . '/services/' . $service['id']) : e($adminPath . '/services') ?>" class="card">
    <?= csrf_field() ?>

    <label for="name">Service Name</label>
    <input type="text" id="name" name="name" value="<?= e($service['name'] ?? '') ?>" required>

    <label for="slug">URL Slug (leave blank to auto-generate)</label>
    <input type="text" id="slug" name="slug" value="<?= e($service['slug'] ?? '') ?>">

    <label for="category_id">Category</label>
    <select id="category_id" name="category_id">
        <option value="">— None —</option>
        <?php foreach ($categories as $c): ?>
            <option value="<?= e((string) $c['id']) ?>" <?= (($service['category_id'] ?? null) == $c['id']) ? 'selected' : '' ?>>
                <?= e($c['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="icon">Icon (Font Awesome class, e.g. fa-laptop-code)</label>
    <input type="text" id="icon" name="icon" value="<?= e($service['icon'] ?? '') ?>" placeholder="fa-laptop-code">

    <label for="summary">Short Summary (shown on the catalogue listing)</label>
    <input type="text" id="summary" name="summary" value="<?= e($service['summary'] ?? '') ?>">

    <label>Full Description</label>
    <div id="editor" style="background:#fff;min-height:200px;border:1px solid #e2e8f0;border-radius:8px;"><?= $service['description'] ?? '' ?></div>
    <textarea name="description" id="content-input" style="display:none;"></textarea>

    <div class="form-section">
        <h2>Pricing</h2>
        <label for="price_type">Price Type</label>
        <select id="price_type" name="price_type">
            <option value="quote" <?= ($service['price_type'] ?? 'quote') === 'quote' ? 'selected' : '' ?>>Quote on request</option>
            <option value="fixed" <?= ($service['price_type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Fixed price</option>
            <option value="starting_from" <?= ($service['price_type'] ?? '') === 'starting_from' ? 'selected' : '' ?>>Starting from</option>
        </select>

        <label for="price">Price (KES — leave blank if "Quote on request")</label>
        <input type="number" id="price" name="price" step="0.01" value="<?= e((string) ($service['price'] ?? '')) ?>">
    </div>

    <div class="form-section">
        <h2>SEO</h2>
        <label for="meta_title">Meta Title</label>
        <input type="text" id="meta_title" name="meta_title" value="<?= e($service['meta_title'] ?? '') ?>">

        <label for="meta_description">Meta Description</label>
        <input type="text" id="meta_description" name="meta_description" value="<?= e($service['meta_description'] ?? '') ?>">
    </div>

    <label for="sort_order">Sort Order</label>
    <input type="number" id="sort_order" name="sort_order" value="<?= e((string) ($service['sort_order'] ?? 0)) ?>">

    <div class="checkbox-row">
        <input type="checkbox" id="is_featured" name="is_featured" value="1" <?= !empty($service['is_featured']) ? 'checked' : '' ?>>
        <label for="is_featured" style="margin:0;font-weight:400;">Show on homepage</label>
    </div>

    <label for="status">Status</label>
    <select id="status" name="status">
        <option value="draft" <?= ($service['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="published" <?= ($service['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
    </select>

    <button type="submit" class="btn btn-primary" style="margin-top:20px;">Save Service</button>
</form>

<div class="form-section">
    <h2>Digital Assistance & Commerce</h2>
    <p class="muted">This controls how AlbaTech sells and fulfils this service. Keep government charges separate from AlbaTech fees.</p>
    <?php
    $commerce = $commerce ?? [];
    $requirements = json_decode((string)($commerce['requirements'] ?? '[]'), true);
    $questions = json_decode((string)($commerce['intake_questions'] ?? '[]'), true);
    $relatedIds = json_decode((string)($commerce['related_service_ids'] ?? '[]'), true);
    if (!is_array($requirements)) $requirements = [];
    if (!is_array($questions)) $questions = [];
    if (!is_array($relatedIds)) $relatedIds = [];
    $questionLines = [];
    foreach ($questions as $q) {
        if (!is_array($q) || empty($q['key']) || empty($q['label'])) continue;
        $questionLines[] = implode('|', [
            (string)$q['key'], (string)$q['label'], (string)($q['type'] ?? 'text'),
            !empty($q['required']) ? 'required' : 'optional', (string)($q['help'] ?? '')
        ]);
    }
    ?>
    <label for="commerce_pricing_mode">Customer pricing</label>
    <select id="commerce_pricing_mode" name="commerce_pricing_mode">
        <?php foreach (['quote'=>'Quote required','fixed'=>'Fixed price','starting_from'=>'Starting from','free'=>'Free / information only'] as $value=>$label): ?>
            <option value="<?= e($value) ?>" <?= (($commerce['pricing_mode'] ?? $service['price_type'] ?? 'quote') === $value) ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
    </select>
    <label for="customer_fee">AlbaTech customer fee (KES)</label>
    <input type="number" id="customer_fee" name="customer_fee" min="0" step="0.01" value="<?= e((string)($commerce['customer_fee'] ?? $service['price'] ?? '')) ?>" placeholder="e.g. 1000">
    <div class="form-grid-2">
        <div><label for="turnaround_min_days">Turnaround minimum (days)</label><input type="number" id="turnaround_min_days" name="turnaround_min_days" min="0" value="<?= e((string)($commerce['turnaround_min_days'] ?? '')) ?>"></div>
        <div><label for="turnaround_max_days">Turnaround maximum (days)</label><input type="number" id="turnaround_max_days" name="turnaround_max_days" min="0" value="<?= e((string)($commerce['turnaround_max_days'] ?? '')) ?>"></div>
    </div>
    <label for="government_fee_note">Government / third-party fee note</label>
    <input type="text" id="government_fee_note" name="government_fee_note" maxlength="500" value="<?= e($commerce['government_fee_note'] ?? '') ?>" placeholder="Official fees are separate and set by the relevant authority.">
    <label for="fee_disclaimer">Pricing disclaimer</label>
    <input type="text" id="fee_disclaimer" name="fee_disclaimer" maxlength="500" value="<?= e($commerce['fee_disclaimer'] ?? '') ?>" placeholder="Price covers AlbaTech assistance only.">

    <label for="commerce_requirements">Requirements — one per line</label>
    <textarea id="commerce_requirements" name="commerce_requirements" rows="6" placeholder="Applicant full name&#10;Phone number&#10;Business name choices"><?= e(implode("\n", array_map('strval', $requirements))) ?></textarea>

    <label for="intake_questions">Service-specific questions</label>
    <p class="muted">One per line: <code>key|Question|type|required|help</code>. Types: text, textarea, select. For select, use comma-separated options in help after a second <code>||</code> is not supported; keep select questions out until options are needed.</p>
    <textarea id="intake_questions" name="intake_questions" rows="7" placeholder="business_name|What is the business name you want to use?|text|required|Give your preferred name&#10;location|Where is the business based?|text|optional|Town or county"><?= e(implode("\n", $questionLines)) ?></textarea>

    <label for="related_service_ids">Related service IDs — comma separated</label>
    <input type="text" id="related_service_ids" name="related_service_ids" value="<?= e(implode(',', array_map('intval', $relatedIds))) ?>" placeholder="12,18,24">

    <div class="checkbox-row">
        <input type="checkbox" id="requires_quote" name="requires_quote" value="1" <?= !empty($commerce['requires_quote']) || (($commerce['pricing_mode'] ?? 'quote') === 'quote') ? 'checked' : '' ?>>
        <label for="requires_quote" style="margin:0;font-weight:400;">Require a quote before payment</label>
    </div>
    <div class="checkbox-row">
        <input type="checkbox" id="instant_request" name="instant_request" value="1" <?= !empty($commerce['instant_request']) ? 'checked' : '' ?>>
        <label for="instant_request" style="margin:0;font-weight:400;">Allow a customer to request this service directly</label>
    </div>
    <div class="checkbox-row">
        <input type="checkbox" id="commerce_active" name="commerce_active" value="1" <?= (($commerce['active'] ?? 1) ? 'checked' : '') ?>>
        <label for="commerce_active" style="margin:0;font-weight:400;">Commerce configuration active</label>
    </div>
    <label for="commerce_internal_notes">Internal operational notes</label>
    <textarea id="commerce_internal_notes" name="commerce_internal_notes" rows="4" placeholder="Internal-only notes for staff."><?= e($commerce['internal_notes'] ?? '') ?></textarea>
</div>

<?php if ($isEdit): ?>
<form method="POST" action="<?= e($adminPath . '/services/' . $service['id'] . '/delete') ?>"
      onsubmit="return confirm('Delete this service permanently?');" style="margin-top:12px;">
    <?= csrf_field() ?>
    <button type="submit" class="btn btn-danger">Delete Service</button>
</form>
<?php endif; ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>
<script src="<?= asset('js/v4/rich-editor.js') ?>"></script>
<?php
$adminContent = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
