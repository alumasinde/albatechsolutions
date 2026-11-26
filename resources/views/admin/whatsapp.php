<?php

use AlbaTech\Core\View;

/** @var string $csrf @var array<int, array<string, mixed>> $conversations @var array<int, array<string, mixed>> $optOuts @var array<int, array<string, mixed>> $thread @var string $threadNumber @var bool $threadOptedOut @var bool $apiConfigured */
$convos = is_array($conversations ?? null) ? $conversations : [];
$outs = is_array($optOuts ?? null) ? $optOuts : [];
$messages = is_array($thread ?? null) ? $thread : [];
?>
<div class="admin-page-actions"><p>Incoming WhatsApp messages, replies and opt-outs. Replies are free-form inside Meta's 24-hour window.</p>
<form method="post" action="/admin/whatsapp/test" data-confirm="Send a test template message to the notification number?"><input type="hidden" name="_csrf" value="<?= View::escape($csrf) ?>"><button class="button button--primary" type="submit"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i> Send test template</button></form></div>

<?php if (!$apiConfigured): ?>
<div class="flash flash-error"><i class="fa-solid fa-circle-exclamation"></i> WhatsApp API is not configured — set WHATSAPP_PHONE_NUMBER_ID and WHATSAPP_ACCESS_TOKEN in .env, then enable the API in Site settings. Inbound messages are still recorded.</div>
<?php endif; ?>

<?php if ($threadNumber !== ''): ?>
<section class="panel editor-section">
    <div class="section-heading"><span><i class="fa-brands fa-whatsapp"></i></span>
        <div><h3>Conversation with +<?= View::escape($threadNumber) ?></h3>
        <p><?php if ($threadOptedOut): ?>This number has opted out — replies are blocked.<?php else: ?>Newest messages last. Replies send as free text.<?php endif; ?> <a href="/admin/whatsapp">Back to inbox</a></p></div>
    </div>
    <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Direction</th><th>Message</th><th>Type</th><th>Status</th><th>Time</th></tr></thead><tbody>
    <?php foreach ($messages as $message): ?><tr>
        <td><?= ($message['direction'] ?? '') === 'in' ? '<span class="status-pill status-pill--published">In</span>' : '<span class="status-pill">Out</span>' ?></td>
        <td><?php if (!empty($message['contact_name']) && ($message['direction'] ?? '') === 'in'): ?><strong><?= View::escape($message['contact_name']) ?>:</strong> <?php endif; ?><?= View::escape((string) ($message['body'] ?? '')) ?></td>
        <td><?= View::escape((string) ($message['message_type'] ?? 'text')) ?></td>
        <td><?= View::escape((string) ($message['status'] ?? '')) ?></td>
        <td><?= View::escape((string) ($message['created_at'] ?? '')) ?></td>
    </tr><?php endforeach; ?>
    <?php if ($messages === []): ?><tr><td colspan="5">No messages recorded for this number yet.</td></tr><?php endif; ?>
    </tbody></table></div>
    <form method="post" action="/admin/whatsapp/<?= View::escape($threadNumber) ?>/send" class="mt-4">
        <input type="hidden" name="_csrf" value="<?= View::escape($csrf) ?>">
        <label class="field full">Reply<textarea name="body" rows="3" maxlength="4000" required placeholder="Type a WhatsApp reply…"<?= $threadOptedOut ? ' disabled' : '' ?>></textarea></label>
        <button class="button button--primary" type="submit"<?= $threadOptedOut ? ' disabled' : '' ?>><i class="fa-solid fa-paper-plane" aria-hidden="true"></i> Send reply</button>
    </form>
</section>
<?php endif; ?>

<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Number</th><th>Contact</th><th>Last message</th><th>Messages</th><th>Last activity</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($convos as $convo): ?><tr>
    <td><a href="/admin/whatsapp/<?= View::escape((string) $convo['wa_number']) ?>">+<?= View::escape((string) $convo['wa_number']) ?></a></td>
    <td><?= View::escape((string) ($convo['contact_name'] ?? '')) ?></td>
    <td><?= ($convo['last_direction'] ?? '') === 'out' ? '<i class="fa-solid fa-reply text-muted" aria-hidden="true"></i> ' : '' ?><?= View::escape(mb_strimwidth((string) ($convo['last_body'] ?? ''), 0, 80, '…')) ?></td>
    <td><?= (int) ($convo['total'] ?? 0) ?></td>
    <td><?= View::escape((string) ($convo['last_at'] ?? '')) ?></td>
    <td><?= !empty($convo['opted_out']) ? '<span class="status-pill status-pill--draft">Opted out</span>' : '<span class="status-pill status-pill--published">Active</span>' ?></td>
    <td><div class="table-actions"><a class="button button--small button--secondary" href="/admin/whatsapp/<?= View::escape((string) $convo['wa_number']) ?>">Open</a></div></td>
</tr><?php endforeach; ?>
<?php if ($convos === []): ?><tr><td colspan="7">No WhatsApp conversations yet. Inbound messages appear here once the webhook receives them.</td></tr><?php endif; ?>
</tbody></table></div>

<?php if ($outs !== []): ?>
<section class="panel editor-section">
    <div class="section-heading"><span><i class="fa-solid fa-ban"></i></span>
        <div><h3>Opt-outs</h3><p>Numbers that replied STOP. They are removed automatically if they reply START.</p></div>
    </div>
    <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Number</th><th>Opted out at</th></tr></thead><tbody>
    <?php foreach ($outs as $out): ?><tr><td>+<?= View::escape((string) $out['wa_number']) ?></td><td><?= View::escape((string) $out['created_at']) ?></td></tr><?php endforeach; ?>
    </tbody></table></div>
</section>
<?php endif; ?>
