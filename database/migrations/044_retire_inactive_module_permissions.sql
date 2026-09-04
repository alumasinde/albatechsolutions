-- Phase 2 retirement markers.
-- Historical data is preserved; these permissions are removed from the active RBAC surface.

DELETE rp
FROM role_permissions rp
INNER JOIN permissions p ON p.id = rp.permission_id
WHERE p.slug IN ('assistant.sessions.view', 'assistant.manage', 'growth.intelligence.view', 'growth.intelligence.manage');

DELETE FROM permissions
WHERE slug IN ('assistant.sessions.view', 'assistant.manage', 'growth.intelligence.view', 'growth.intelligence.manage');
