<?php

use AlbaTech\Core\View;

/** @var string $csrf */
$convos = is_array($conversations ?? null) ? $conversations : [];
$outs = is_array($optOuts ?? null) ? $optOuts : [];
$messages = is_array($thread ?? null) ? $thread : [];
$contact = is_array($contact ?? null) ? $contact : [];
$consent = is_array($consent ?? null) ? $consent : null;
$health = is_array($health ?? null) ? $health : [];
$windowOpen = !empty($contact['window_open']);
$botPaused = !empty($contact['bot_paused']);
$topicLabels = ['compliance' => 'Compliance help', 'build' => 'Website / system', 'human' => 'Asked for a person'];
$originLabels = ['customer' => 'Customer', 'bot' => 'Auto-reply', 'agent' => 'You', 'system' => 'System'];
$statusClass = static fn (string $s): string => match ($s) { 'read', 'delivered', 'sent', 'received' => 'status-pill--published', 'queued' => 'status-pill--draft', 'failed', 'cancelled' => 'status-pill--archived', default => '' };
$lastEvent = (string) ($health['last_event_at'] ?? '');
?>
<div class="admin-page-actions"><p>Incoming WhatsApp messages and replies. Free-text replies work inside Meta's 24-hour window; after that, send the follow-up template.</p>
<form method="post" action="/admin/whatsapp/test" data-confirm="Send a test template message to the notification number?"><input type="hidden" name="_csrf" value="<?= View::escape($csrf) ?>"><button class="button button--secondary" type="submit"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i> Send test template</button></form></div>

<?php if (empty($apiConfigured) || empty($apiEnabled)): ?>
<div class="flash flash-error"><i class="fa-solid fa-circle-exclamation"></i> <?= empty($apiConfigured) ? 'WhatsApp API credentials are missing in .env (WHATSAPP_PHONE_NUMBER_ID, WHATSAPP_ACCESS_TOKEN, WHATSAPP_APP_SECRET).' : 'The WhatsApp API is disabled in Site settings, so no automatic replies are sent.' ?> Signed inbound messages are still recorded.</div>
<?php endif; ?>

<section class="panel editor-section">
    <div class="section-heading"><span><i class="fa-solid fa-heart-pulse"></i></span><div><h3>Health</h3><p>Checks that Meta is reaching the site and messages are going out.</p></div></div>
    <ul class="grid gap-1 text-sm">
        <li><strong>Last webhook received:</strong> <?= $lastEvent !== '' ? View::escape($lastEvent) : 'never' ?></li>
        <li><strong>Queued for retry:</strong> <?= (int) ($health['queued'] ?? 0) ?></li>
        <li><strong>Failed sends (7 days):</strong> <?= (int) ($health['failed_7d'] ?? 0) ?></li>
        <li><strong>Webhook events that failed processing:</strong> <?= (int) ($health['events_failed'] ?? 0) ?><?= (int) ($health['events_failed'] ?? 0) > 0 ? ' (the worker retries them; check the PHP error log if this grows)' : '' ?></li>
    </ul>
</section>

<?php if ($threadNumber !== ''): ?>
<section class="panel editor-section">
    <div class="section-heading"><span><i class="fa-brands fa-whatsapp"></i></span>
        <div><h3>Conversation with +<?= View::escape($threadNumber) ?><?= !empty($contact['contact_name']) ? ' (' . View::escape((string) $contact['contact_name']) . ')' : '' ?></h3>
        <p><a href="/admin/whatsapp">Back to inbox</a></p></div>
    </div>
    <ul class="mb-4 grid gap-1 text-sm">
        <li><strong>Reply window:</strong> <?= $windowOpen ? '<span class="status-pill status-pill--published">Open</span> until ' . View::escape((string) ($contact['window_closes_at'] ?? '')) : '<span class="status-pill status-pill--draft">Closed</span> send the follow-up template' ?></li>
        <li><strong>Automatic replies:</strong> <?= $botPaused ? 'Paused until ' . View::escape((string) $contact['bot_paused_until']) . ' (a person is handling this chat)' : 'Active' ?></li>
        <?php if (!empty($contact['topic'])): ?><li><strong>Topic:</strong> <?= View::escape($topicLabels[$contact['topic']] ?? (string) $contact['topic']) ?></li><?php endif; ?>
        <?php if (!empty($contact['source_page'])): ?><li><strong>Started from:</strong> <code><?= View::escape((string) $contact['source_page']) ?></code></li><?php endif; ?>
        <?php if ($consent !== null): ?><li><strong>Consent:</strong> <?= View::escape((string) $consent['source']) ?> on <?= View::escape((string) $consent['created_at']) ?><?= !empty($consent['reference']) ? ', ref ' . View::escape((string) $consent['reference']) : '' ?></li><?php endif; ?>
        <?php if ($threadOptedOut): ?><li><span class="status-pill status-pill--draft">Opted out</span> No automatic or template messages. You can still reply personally while the window is open.</li><?php endif; ?>
    </ul>
    <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>From</th><th>Message</th><th>Status</th><th>Time</th></tr></thead><tbody>
    <?php foreach ($messages as $message): $status = (string) ($message['status'] ?? ''); ?><tr>
        <td><?= ($message['direction'] ?? '') === 'in' ? '<span class="status-pill status-pill--published">' : '<span class="status-pill">' ?><?= View::escape($originLabels[(string) ($message['origin'] ?? '')] ?? 'Out') ?></span></td>
        <td class="wa-body"><?= View::escape((string) ($message['body'] ?? '')) ?>
            <?php if (!empty($message['media_path'])): ?><br><a class="button button--small button--secondary mt-2" href="/admin/whatsapp/media/<?= (int) $message['id'] ?>" target="_blank" rel="noopener"><i class="fa-solid fa-paperclip" aria-hidden="true"></i> Open file</a><?php endif; ?>
            <?php if (!empty($message['error_message']) && in_array($status, ['failed', 'queued'], true)): ?><br><small class="text-muted"><?= View::escape('(#' . (string) $message['error_code'] . ') ' . (string) $message['error_message']) ?></small><?php endif; ?>
        </td>
        <td><?php if ($status !== ''): ?><span class="status-pill <?= $statusClass($status) ?>"><?= View::escape($status) ?></span><?php endif; ?></td>
        <td><?= View::escape((string) ($message['created_at'] ?? '')) ?></td>
    </tr><?php endforeach; ?>
    <?php if ($messages === []): ?><tr><td colspan="4">No messages recorded for this number yet.</td></tr><?php endif; ?>
    </tbody></table></div>

    <?php if ($windowOpen): ?>
    <form method="post" action="/admin/whatsapp/<?= View::escape($threadNumber) ?>/send" class="mt-4">
        <input type="hidden" name="_csrf" value="<?= View::escape($csrf) ?>">
        <label class="field full">Reply<textarea name="body" rows="3" maxlength="4000" required placeholder="Type a WhatsApp reply…"></textarea><small>Sending pauses automatic replies for this chat for 24 hours.</small></label>
        <button class="button button--primary" type="submit"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i> Send reply</button>
    </form>
    <?php elseif (!$threadOptedOut): ?>
    <form method="post" action="/admin/whatsapp/<?= View::escape($threadNumber) ?>/template" class="mt-4">
        <input type="hidden" name="_csrf" value="<?= View::escape($csrf) ?>">
        <p class="text-sm">The 24-hour window is closed, so WhatsApp only allows an approved template. <?= ($reopenTemplate ?? '') !== '' ? 'This sends <code>' . View::escape($reopenTemplate) . '</code>.' : 'Set a follow-up template in Site settings first.' ?></p>
        <button class="button button--primary" type="submit"<?= ($reopenTemplate ?? '') === '' ? ' disabled' : '' ?>><i class="fa-solid fa-paper-plane" aria-hidden="true"></i> Send follow-up template</button>
    </form>
    <?php endif; ?>

    <div class="table-actions mt-4">
        <?php if ($botPaused): ?>
        <form method="post" action="/admin/whatsapp/<?= View::escape($threadNumber) ?>/resume-bot"><input type="hidden" name="_csrf" value="<?= View::escape($csrf) ?>"><button class="button button--small button--secondary" type="submit"><i class="fa-solid fa-robot" aria-hidden="true"></i> Resume automatic replies</button></form>
        <?php endif; ?>
        <form method="post" action="/admin/whatsapp/<?= View::escape($threadNumber) ?>/delete" data-confirm="Permanently delete this conversation, its files and consent records? This cannot be undone."><input type="hidden" name="_csrf" value="<?= View::escape($csrf) ?>"><button class="button button--small button--danger" type="submit"><i class="fa-solid fa-trash" aria-hidden="true"></i> Delete conversation (data request)</button></form>
    </div>
</section>
<?php endif; ?>

<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Number</th><th>Contact</th><th>Topic</th><th>Last message</th><th>Last activity</th><th>Window</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($convos as $convo): ?><tr>
    <td><a href="/admin/whatsapp/<?= View::escape((string) $convo['wa_number']) ?>">+<?= View::escape((string) $convo['wa_number']) ?></a></td>
    <td><?= View::escape((string) ($convo['contact_name'] ?? '')) ?><?= !empty($convo['opted_out']) ? ' <span class="status-pill status-pill--draft">Opted out</span>' : '' ?></td>
    <td><?= View::escape($topicLabels[(string) ($convo['topic'] ?? '')] ?? '') ?></td>
    <td><?= ($convo['last_direction'] ?? '') === 'out' ? '<i class="fa-solid fa-reply text-muted" aria-hidden="true"></i> ' : '' ?><?= View::escape(mb_strimwidth((string) ($convo['last_body'] ?? ''), 0, 80, '…')) ?></td>
    <td><?= View::escape((string) ($convo['last_at'] ?? '')) ?></td>
    <td><?= !empty($convo['window_open']) ? '<span class="status-pill status-pill--published">Open</span>' : '<span class="status-pill">Closed</span>' ?></td>
    <td><div class="table-actions"><a class="button button--small button--secondary" href="/admin/whatsapp/<?= View::escape((string) $convo['wa_number']) ?>">Open</a></div></td>
</tr><?php endforeach; ?>
<?php if ($convos === []): ?><tr><td colspan="7">No WhatsApp conversations yet. Inbound messages appear here once the webhook receives them.</td></tr><?php endif; ?>
</tbody></table></div>

<?php if ($outs !== []): ?>
<section class="panel editor-section">
    <div class="section-heading"><span><i class="fa-solid fa-ban"></i></span>
        <div><h3>Opt-outs</h3><p>Numbers that replied STOP. They get no automatic or template messages until they reply START.</p></div>
    </div>
    <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Number</th><th>Opted out at</th></tr></thead><tbody>
    <?php foreach ($outs as $out): ?><tr><td>+<?= View::escape((string) $out['wa_number']) ?></td><td><?= View::escape((string) $out['created_at']) ?></td></tr><?php endforeach; ?>
    </tbody></table></div>
</section>
<?php endif; ?>
