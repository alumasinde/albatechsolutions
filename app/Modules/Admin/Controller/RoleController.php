<?php

declare(strict_types=1);

namespace App\Modules\Admin\Controller;

use App\Core\AuditLog;
use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Admin\Repository\PermissionRepository;
use App\Modules\Admin\Repository\RoleRepository;

final class RoleController extends BaseController
{
    public function __construct(
        private readonly RoleRepository $roles,
        private readonly PermissionRepository $permissions
    ) {
    }

    public function index(Request $request): Response
    {
        return $this->view('admin.roles.index', [
            'roles' => $this->roles->allWithPermissionCount(),
        ]);
    }

    public function edit(Request $request): Response
    {
        $roleId = (int) $request->param('id');

        return $this->view('admin.roles.edit', [
            'role' => $this->roles->find($roleId),
            'permissionGroups' => $this->permissions->allGroupedByModule(),
            'assignedPermissionIds' => $this->roles->permissionIdsForRole($roleId),
        ]);
    }

    public function update(Request $request): Response
    {
        $roleId = (int) $request->param('id');
        $permissionIds = array_map('intval', (array) $request->input('permission_ids', []));

        $this->roles->syncPermissions($roleId, $permissionIds);
        AuditLog::record('role.permissions_updated', 'role', $roleId, ['permission_ids' => $permissionIds]);

        Session::flash('_success', 'Role permissions updated.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/roles');
    }
}
