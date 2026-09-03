<?php $title='My AlbaTech Account'; ob_start(); ?>
<section class="customer-hero"><div><span class="eyebrow">Your AlbaTech account</span><h1>Welcome back, <?= e($user['name'] ?? 'Customer') ?>.</h1><p>Keep your digital assistance requests, quotes, payments and receipts together.</p></div><a class="btn btn-primary" href="/get-help"><i class="fa-solid fa-plus"></i> Get help</a></section>
<div class="customer-stats">
    <div class="card"><span class="eyebrow">Requests</span><strong><?= (int)($stats['requests_total']??0) ?></strong><span>Total requests</span></div>
    <div class="card"><span class="eyebrow">Active</span><strong><?= (int)($stats['active_requests']??0) ?></strong><span>Being handled</span></div>
    <div class="card"><span class="eyebrow">Completed</span><strong><?= (int)($stats['completed_requests']??0) ?></strong><span>Finished work</span></div>
</div>
<section class="customer-section"><div class="section-heading"><div><span class="eyebrow">Recent activity</span><h2>What is happening?</h2></div><a href="/account/requests">View all</a></div>
<?php if(!$activity): ?><div class="card empty-state"><h3>No requests yet</h3><p>When you ask AlbaTech for help, your requests will appear here.</p><a class="btn btn-primary" href="/get-help">Tell us what you need</a></div><?php else: ?><div class="customer-list"><?php foreach($activity as $item): ?><a class="card customer-list-row" href="/account/requests/<?= (int)$item['id'] ?>"><div><strong><?= e($item['service_name'] ?: 'Digital assistance') ?></strong><span><?= e($item['request_number']) ?></span></div><span class="status-pill status-<?= e($item['status']) ?>"><?= e(str_replace('_',' ',$item['status'])) ?></span></a><?php endforeach; ?></div><?php endif; ?></section>
<?php $customerContent=ob_get_clean(); require __DIR__.'/layout.php'; ?>
