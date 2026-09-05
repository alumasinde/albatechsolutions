<?php
$title = 'Get Digital Help in Kenya | AlbaTech Solutions';
$metaDescription = 'Tell AlbaTech what you are trying to do online. Get practical digital assistance for Kenyan government services, business, documents, CVs and websites.';
$canonicalUrl = rtrim(config('app.url'), '/') . '/get-help';
$jsonLd = [[
    '@context' => 'https://schema.org',
    '@type' => 'ContactPage',
    'name' => $title,
    'url' => $canonicalUrl,
    'description' => $metaDescription,
]];
ob_start();
$old = $old ?? [];
$errors = $errors ?? [];
$categoryLabels = [
    'government' => 'Government & online services',
    'business' => 'Start or sort out my business',
    'documents' => 'Documents & applications',
    'jobs' => 'CV & job help',
    'website' => 'Get my business online',
    'software' => 'Build a system',
    'other' => "I'm not sure / something else",
];
?>
<section class="conversion-hero">
  <div class="public-container conversion-hero__grid">
    <div>
      <span class="public-kicker">AlbaTech digital assistance</span>
      <h1>Tell us what you're trying to do.</h1>
      <p>You don't need to know the name of the service. Explain the task in plain language and we'll help you work out the next step.</p>
      <div class="conversion-hero__trust"><span><i class="fa-solid fa-user"></i> Human help</span><span><i class="fa-solid fa-shield-halved"></i> Clear process</span><span><i class="fa-brands fa-whatsapp"></i> WhatsApp friendly</span></div>
    </div>
    <aside class="conversion-hero__note"><strong>Before you send anything</strong><p>Do not include passwords, PINs, OTPs, bank details or other secrets in this form. We'll tell you what information is actually needed.</p></aside>
  </div>
</section>

<section class="conversion-section">
  <div class="public-container conversion-layout">
    <div class="conversion-form-card">
      <div class="conversion-form-head"><span class="public-kicker">Get assistance</span><h2>What do you need help with?</h2><p>One request is enough. We'll contact you using your preferred method.</p></div>
      <?php if ($errors): ?><div class="alert alert-error" role="alert"><strong>Please check the form.</strong><ul><?php foreach ($errors as $fieldErrors) foreach ((array)$fieldErrors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
      <form method="POST" action="/get-help" class="conversion-form" novalidate>
        <?= csrf_field() ?>
        <div class="conversion-hp" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
        <div class="conversion-grid-2">
          <label>Your name *<input name="name" required maxlength="120" value="<?= e($old['name'] ?? '') ?>" autocomplete="name"></label>
          <label>Phone / WhatsApp *<input name="phone" required maxlength="20" value="<?= e($old['phone'] ?? '') ?>" autocomplete="tel" inputmode="tel"></label>
          <label>Email <span class="conversion-optional">optional</span><input type="email" name="email" maxlength="190" value="<?= e($old['email'] ?? '') ?>" autocomplete="email"></label>
          <label>Preferred contact *<select name="preferred_contact" required><option value="">Choose one</option><?php foreach(['whatsapp'=>'WhatsApp','phone'=>'Phone call','email'=>'Email'] as $value=>$label): ?><option value="<?= e($value) ?>" <?= ($old['preferred_contact'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
        </div>
        <fieldset class="conversion-choice-group"><legend>What are you trying to do? *</legend><div class="conversion-choice-grid"><?php foreach ($categoryLabels as $value=>$label): ?><label class="conversion-choice"><input type="radio" name="category" value="<?= e($value) ?>" <?= ($old['category'] ?? '') === $value ? 'checked' : '' ?>><span><?= e($label) ?></span></label><?php endforeach; ?></div></fieldset>
        <label>Specific service <span class="conversion-optional">optional</span><select name="service_id" id="conversion-service-select"><option value="">I'm not sure</option><?php foreach ($services as $service): ?><option value="<?= (int)$service['id'] ?>" <?= (string)($old['service_id'] ?? '') === (string)$service['id'] ? 'selected' : '' ?>><?= e($service['name']) ?></option><?php endforeach; ?></select></label>
        <?php foreach ($services as $service): $qs = json_decode((string)($service['intake_questions'] ?? '[]'), true); if (!is_array($qs) || !$qs) continue; ?>
        <div class="conversion-service-questions" data-service-id="<?= (int)$service['id'] ?>" hidden>
          <h3><?= e($service['name']) ?> — a few details</h3>
          <?php foreach ($qs as $q): if (!is_array($q) || empty($q['key']) || empty($q['label'])) continue; $key=preg_replace('/[^a-zA-Z0-9_-]/','',(string)$q['key']); ?>
            <label><?= e($q['label']) ?> <?= !empty($q['required']) ? '*' : '<span class="conversion-optional">optional</span>' ?>
              <?php if (($q['type'] ?? 'text') === 'textarea'): ?><textarea name="intake_answers[<?= e($key) ?>]" rows="4" maxlength="1000" placeholder="<?= e($q['help'] ?? '') ?>"><?= e($old['intake_answers'][$key] ?? '') ?></textarea><?php else: ?><input type="text" name="intake_answers[<?= e($key) ?>]" maxlength="1000" value="<?= e($old['intake_answers'][$key] ?? '') ?>" placeholder="<?= e($q['help'] ?? '') ?>"><?php endif; ?>
            </label>
          <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        <label>Explain what you're trying to do *<textarea name="message" required maxlength="3000" rows="7" placeholder="For example: I want to register a business but I am not sure which steps or documents I need."><?= e($old['message'] ?? '') ?></textarea></label>
        <label class="conversion-consent"><input type="checkbox" name="consent" value="1" <?= ($old['consent'] ?? '') === '1' ? 'checked' : '' ?> required><span>I agree that AlbaTech may use these details to respond to this assistance request.</span></label>
        <button class="btn btn-primary btn-lg" type="submit">Send assistance request <i class="fa-solid fa-arrow-right"></i></button>
      </form>
    </div>
    <aside class="conversion-side">
      <div class="conversion-side-card"><span class="conversion-side-icon"><i class="fa-solid fa-list-check"></i></span><h3>What happens next?</h3><ol><li><b>You explain the task.</b><span>No technical language needed.</span></li><li><b>We review it.</b><span>We work out what help is appropriate.</span></li><li><b>We contact you.</b><span>We clarify requirements and any applicable charges.</span></li><li><b>You decide.</b><span>No work starts without your agreement.</span></li></ol></div>
      <div class="conversion-side-card conversion-side-card--soft"><h3>Need help right now?</h3><p>If WhatsApp is easier, you can start a conversation directly.</p><?php if (setting('whatsapp_number')): ?><a class="btn btn-secondary js-whatsapp" data-whatsapp-number="<?= e(preg_replace('/\D+/', '', (string)setting('whatsapp_number'))) ?>" href="<?= e(whatsapp_url('Hi AlbaTech Solutions, I need help with something online.')) ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp</a><?php endif; ?></div>
      <div class="conversion-disclaimer"><i class="fa-solid fa-circle-info"></i><span>AlbaTech is an independent digital assistance business, not a government agency. Official fees, requirements and processing times are set by the relevant agency.</span></div>
    </aside>
  </div>
</section>
<script>
(function(){
  const select=document.getElementById('conversion-service-select');
  const blocks=[...document.querySelectorAll('.conversion-service-questions')];
  function sync(){ const id=select?.value||''; blocks.forEach(b=>{const active=b.dataset.serviceId===id; b.hidden=!active; b.querySelectorAll('input,textarea,select').forEach(el=>{el.disabled=!active;});}); }
  select?.addEventListener('change',sync); sync();
})();
</script>
<?php $pageContent = ob_get_clean(); require dirname(__DIR__) . '/layout.php';
