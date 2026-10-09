<?php

use AlbaTech\Core\View;

$s = $service ?? [];
$val = static fn(string $key, mixed $fallback = '') => View::escape($s[$key] ?? $fallback); 
/**
 * @var array $service
 * @var array $categories
 * @var string $formAction
 * @var string $csrf
 */
?>
<div class="form-page-head"><a class="back-link" href="/admin/services"><i class="fa-solid fa-arrow-left"></i> Back to services</a>
    <p>Write for customers first. Keep the offer specific, useful and easy to understand.</p>
</div><?php if (!empty($errors)): ?><div class="form-error">
        <ul><?php foreach ($errors as $error): ?><li><?= View::escape($error) ?></li><?php endforeach; ?></ul>
    </div><?php endif; ?><form method="post" action="<?= View::escape($formAction) ?>" class="service-editor"><input type="hidden" name="_csrf" value="<?= View::escape($csrf) ?>">
    <div class="editor-main">
        <section class="panel editor-section">
            <div class="section-heading"><span>01</span>
                <div>
                    <h3>Service information</h3>
                    <p>The name and details customers will see.</p>
                </div>
            </div>
            <div class="form-grid"><label class="field full">Service name <input name="name" required maxlength="180" value="<?= $val('name') ?>" placeholder="e.g. Business Website Design"></label><label class="field full">URL slug <input name="slug" maxlength="200" value="<?= $val('slug') ?>" placeholder="business-website-design"><small>Used in the service URL. Use lowercase words separated by hyphens.</small></label><label class="field full">Category <select name="category_id" required>
                        <option value="">Choose a category</option><?php foreach ($categories as $category): ?><option value="<?= (int)$category['id'] ?>" <?= (string)($s['category_id'] ?? '') === (string)$category['id'] ? 'selected' : '' ?>><?= View::escape($category['name']) ?></option><?php endforeach; ?>
                    </select></label><label class="field full">Short description <textarea name="short_description" rows="3" maxlength="500" required placeholder="A concise summary shown on service cards."><?= $val('short_description') ?></textarea><small>Up to 500 characters. Focus on the customer's outcome.</small></label><label class="field full">Full description <textarea name="description" rows="6" placeholder="Explain what the service does, what customers can expect, and any important boundaries."><?= $val('description') ?></textarea></label><label class="field">Who is this for? <textarea name="who_for" rows="4" placeholder="The people or businesses this helps."><?= $val('who_for') ?></textarea></label><label class="field">What's included? <textarea name="what_included" rows="4" placeholder="List deliverables or scope."><?= $val('what_included') ?></textarea></label><label class="field full">What the customer needs to provide <textarea name="what_needed" rows="3" placeholder="Information, documents or materials needed to begin."><?= $val('what_needed') ?></textarea></label></div>
        </section>
        <section class="panel editor-section">
            <div class="section-heading"><span>02</span>
                <div>
                    <h3>Search appearance</h3>
                    <p>Optional page title and description for search engines.</p>
                </div>
            </div>
            <div class="form-grid"><label class="field full">SEO title <input name="meta_title" maxlength="255" value="<?= $val('meta_title') ?>" placeholder="Service name in Kenya | AlbaTech Solutions"></label><label class="field full">SEO description <textarea name="meta_description" rows="3" maxlength="320" placeholder="A useful summary of this service for search results."><?= $val('meta_description') ?></textarea></label></div>
        </section>
    </div>
    <aside class="editor-side">
        <section class="panel editor-section">
            <h3>Publish settings</h3><label class="field">Website visibility<select name="status">
                    <option value="draft" <?= ($s['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Off — hidden from website</option>
                    <option value="published" <?= ($s['status'] ?? '') === 'published' ? 'selected' : '' ?>>On — published</option>
                    <option value="archived" <?= ($s['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
                </select></label><label class="check-field"><input type="checkbox" name="is_featured" value="1" <?= (int)($s['is_featured'] ?? 0) === 1 ? 'checked' : '' ?>><span><strong>Feature on homepage</strong><small>Only appears while the service is published and its category is active.</small></span></label><label class="field">Display order <input type="number" name="sort_order" min="0" max="99999" value="<?= $val('sort_order', 0) ?>"><small>Smaller numbers appear first.</small></label>
        </section>
        <section class="panel editor-section">
            <div class="section-heading"><span>03</span>
                <div>
                    <h3>Pricing</h3>
                    <p>Choose what visitors should see.</p>
                </div>
            </div><label class="field">Pricing display<select name="pricing_type" id="pricingType">
                    <option value="quote" <?= ($s['pricing_type'] ?? 'quote') === 'quote' ? 'selected' : '' ?>>Contact for price</option>
                    <option value="fixed" <?= ($s['pricing_type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Fixed price</option>
                    <option value="starting_at" <?= ($s['pricing_type'] ?? '') === 'starting_at' ? 'selected' : '' ?>>Starting price</option>
                </select></label><label class="field" id="priceField">Price in KSh<input type="number" name="pricing_amount" id="pricingAmount" min="0" step="0.01" value="<?= $val('pricing_amount') ?>" placeholder="e.g. 5000"><small>Enter your actual price. Do not use sample prices.</small></label>
            <div class="price-preview"><small>Customer display preview</small><strong id="pricePreview"><?php if (($s['pricing_type'] ?? 'quote') === 'fixed' && ($s['pricing_amount'] ?? '') !== ''): ?>KSh <?= View::escape(number_format((float)$s['pricing_amount'], 0)) ?><?php elseif (($s['pricing_type'] ?? '') === 'starting_at' && ($s['pricing_amount'] ?? '') !== ''): ?>From KSh <?= View::escape(number_format((float)$s['pricing_amount'], 0)) ?><?php else: ?>Contact for price<?php endif; ?></strong></div>
        </section>
        <div class="editor-actions"><button class="btn btn-primary btn-full" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save service</button><a class="btn btn-light btn-full" href="/admin/services">Cancel</a></div>
    </aside>
</form>
<script>
    (function() {
        var type = document.getElementById('pricingType'),
            amount = document.getElementById('pricingAmount'),
            field = document.getElementById('priceField'),
            preview = document.getElementById('pricePreview');

        function update() {
            var t = type.value,
                n = parseFloat(amount.value);
            field.hidden = t === 'quote';
            preview.textContent = t === 'quote' ? 'Contact for price' : (t === 'starting_at' ? 'From ' : '') + 'KSh ' + (Number.isFinite(n) && amount.value !== '' ? new Intl.NumberFormat('en-KE', {
                maximumFractionDigits: 2
            }).format(n) : '—');
        }
        type.addEventListener('change', update);
        amount.addEventListener('input', update);
        update();
    })();
</script>