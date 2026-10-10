<?php
use AlbaTech\Core\View;
use AlbaTech\Repositories\SiteSettingsRepository;
$legalSettings = (new SiteSettingsRepository())->publicValues();
$legalBody = trim((string) ($legalSettings["privacy_content"] ?? ""));
?>
<section class="legal-hero">
  <div class="container legal-hero__inner">
    <span class="eyebrow">Your information</span>
    <h1>Privacy policy</h1>
    <p>We aim to handle enquiry information carefully and use it only for legitimate service and communication purposes.</p>
    <p class="legal-updated">Last updated: 9 October 2026</p>
  </div>
</section>
<section class="section">
  <div class="container legal-layout">
    <aside class="legal-sidebar"><strong>On this page</strong><a href="#information">Information you share</a><a href="#use">How information is used</a><a href="#sharing">Sharing and providers</a><a href="#security">Security and retention</a><a href="#choices">Your choices</a><a href="#contact">Contact</a></aside>
    <article class="legal-content">
      <?php if ($legalBody !== ''): ?>
        <?php foreach (preg_split("/\R{2,}/", $legalBody) ?: [] as $paragraph): $paragraph = trim($paragraph); if ($paragraph === '') continue; ?>
          <?php if (str_starts_with($paragraph, '# ')): ?><h2><?= View::escape(substr($paragraph, 2)) ?></h2><?php else: ?><p><?= nl2br(View::escape($paragraph)) ?></p><?php endif; ?>
        <?php endforeach; ?>
      <?php else: ?>
      <section id="information"><h2>1. Information you share</h2><p>When you contact us, you may choose to share details such as your name, phone number, email address, project requirements or information needed to respond to your enquiry. Please share only what is reasonably necessary for the task.</p></section>
      <section id="use"><h2>2. How information is used</h2><p>Information may be used to respond to you, understand a requested service, prepare a quote, deliver agreed work, provide support and maintain appropriate business records. We do not ask you to send passwords, PINs, one-time verification codes or M-Pesa credentials through an enquiry.</p></section>
      <section id="sharing"><h2>3. Sharing and service providers</h2><p>We do not intend to sell your personal information. Information may be processed by communication, hosting or other service providers where needed to operate the website or provide a requested service, or disclosed where required by law. External services have their own privacy practices and terms.</p></section>
      <section id="security"><h2>4. Security and retention</h2><p>Reasonable safeguards should be used to protect information from unauthorised access, loss or misuse. No internet transmission or storage method can be guaranteed completely secure. Information should be retained only for as long as reasonably needed for the purpose collected, business records or applicable legal obligations.</p></section>
      <section id="choices"><h2>5. Your choices</h2><p>You can choose what information to include in an enquiry. You may contact us to ask about information you have shared, request a correction or raise a privacy concern. Applicable rights and obligations depend on the circumstances and Kenyan law.</p></section>
      <section id="contact"><h2>6. Updates and contact</h2><p>This policy may be updated as our website and services change. The current version will be published here. For a privacy question, <a href="/get-help">contact us</a>.</p></section>
      <p class="legal-disclaimer">This is a starter privacy notice, not legal advice or a completed compliance assessment. Review the actual data collected by your website, analytics, hosting and communication providers, and have the policy checked for compliance with Kenya's Data Protection Act and applicable regulations before launch.</p>
    
      <?php endif; ?>
</article>
  </div>
</section>
