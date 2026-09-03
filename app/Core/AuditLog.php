<?php

declare(strict_types=1);

namespace App\Core;

/**
 * AuditLog - records who did what, when, from where. Used across
 * modules for compliance and activity-history views (e.g. "Order
 * #1042 status changed by Jane Doe on 2026-07-28").
 */
final class AuditLog
{
    public static function record(string $action, ?string $entityType = null, ?int $entityId = null, array $meta = []): void
    {
        $request = new Request();

        $stmt = Database::connection()->prepare(
            'INSERT INTO audit_logs (user_id, action, entity_type, entity_id, meta, ip_address, user_agent, created_at)
             VALUES (:user_id, :action, :entity_type, :entity_id, :meta, :ip, :ua, NOW())'
        );

        $stmt->execute([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'meta'        => $meta ? json_encode($meta, JSON_UNESCAPED_SLASHES) : null,
            'ip'          => $request->ip(),
            'ua'          => $request->userAgent(),
        ]);
    }
}
