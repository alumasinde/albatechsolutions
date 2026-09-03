<?php

declare(strict_types=1);

namespace App\Modules\Admin\Controller;

use App\Core\BaseController;
use App\Core\Config;
use App\Core\Helpers\Validator;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Admin\Repository\RoleRepository;
use App\Modules\Admin\Service\UserService;
use App\Modules\Auth\Repository\UserRepository;

final class UserController extends BaseController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UserRepository $users,
        private readonly RoleRepository $roles
    ) {
    }

    public function index(Request $request): Response
    {
        return $this->view('admin.users.index', [
            'users' => $this->users->allWithRoles(),
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->view('admin.users.form', [
            'roles' => $this->roles->allWithPermissionCount(),
            'selectedRoleIds' => [],
            'user' => null,
        ]);
    }

    public function store(Request $request): Response
    {
        $validator = new Validator($request->all(), [
            'name'     => 'required|min:2|max:150',
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            Session::flash('_errors', $validator->errors());

            return $this->back();
        }

        $result = $this->userService->create(
            $request->only(['name', 'email', 'phone', 'password']),
            (array) $request->input('role_ids', [])
        );

        if (!$result['success']) {
            Session::flash('_errors', ['email' => [$result['message']]]);

            return $this->back();
        }

        Session::flash('_success', 'User created.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/users');
    }

    public function toggleActive(Request $request): Response
    {
        $userId = (int) $request->param('id');
        $user = $this->users->find($userId);

        if ($user) {
            $this->userService->setActive($userId, !$user['is_active']);
            Session::flash('_success', 'User status updated.');
        }

        return $this->redirect(Config::get('admin.path', '/admin') . '/users');
    }
}
