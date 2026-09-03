<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controller;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Config;
use App\Core\Helpers\Validator;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Auth\Service\AuthService;

final class AuthController extends BaseController
{
    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    public function showLogin(Request $request): Response
    {
        if (Auth::check()) {
            return $this->redirect('/dashboard');
        }

        return $this->view('auth.login');
    }

    public function showRegister(Request $request): Response
    {
        if (Auth::check()) {
            return $this->redirect('/dashboard');
        }

        return $this->view('auth.register');
    }

    public function register(Request $request): Response
    {
        $validator = new Validator($request->all(), [
            'name'     => 'required|min:2|max:150',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            Session::flash('_errors', $validator->errors());
            Session::flash('_old', $request->only(['name', 'email', 'phone']));

            return $this->back();
        }

        $result = $this->authService->register($request->only(['name', 'email', 'phone', 'password']));

        if (!$result['success']) {
            Session::flash('_errors', ['email' => [$result['message']]]);
            Session::flash('_old', $request->only(['name', 'email', 'phone']));

            return $this->back();
        }

        return $this->redirect($this->intendedUrl());
    }

    public function login(Request $request): Response
    {
        $validator = new Validator($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            Session::flash('_errors', $validator->errors());
            Session::flash('_old', $request->only(['email']));

            return $this->back();
        }

        $result = $this->authService->attemptLogin(
            (string) $request->input('email'),
            (string) $request->input('password')
        );

        if (!$result['success']) {
            Session::flash('_errors', ['login' => [$result['message']]]);

            return $this->back();
        }

        if (!empty($result['requires_2fa'])) {
            return $this->redirect(Config::get('auth.login_path', '/login') . '/verify');
        }

        return $this->redirect($this->intendedUrl());
    }

    public function showTwoFactorChallenge(Request $request): Response
    {
        if (Auth::pendingTwoFactorUserId() === null) {
            return $this->redirect(Config::get('auth.login_path', '/login'));
        }

        return $this->view('auth.two-factor-challenge');
    }

    public function verifyTwoFactorChallenge(Request $request): Response
    {
        if (Auth::pendingTwoFactorUserId() === null) {
            return $this->redirect(Config::get('auth.login_path', '/login'));
        }

        $result = $this->authService->verifyTwoFactorChallenge((string) $request->input('code', ''));

        if (!$result['success']) {
            Session::flash('_errors', ['code' => [$result['message']]]);

            return $this->back();
        }

        return $this->redirect($this->intendedUrl());
    }

    public function verifyEmail(Request $request): Response
    {
        $result = $this->authService->verifyEmail((string) $request->param('token'));

        Session::flash($result['success'] ? '_success' : '_errors', $result['success']
            ? 'Your email address has been verified.'
            : ['token' => [$result['message']]]);

        return $this->redirect(Auth::check() ? '/dashboard' : Config::get('auth.login_path', '/login'));
    }

    public function resendVerification(Request $request): Response
    {
        if (!Auth::check()) {
            return $this->redirect(Config::get('auth.login_path', '/login'));
        }

        $result = $this->authService->resendEmailVerification((int) Auth::id());

        Session::flash($result['success'] ? '_success' : '_errors', $result['success']
            ? $result['message']
            : ['email' => [$result['message']]]);

        return $this->back();
    }

    public function logout(Request $request): Response
    {
        Auth::logout();

        return $this->redirect(Config::get('auth.login_path', '/login'));
    }

    /**
     * Where AuthMiddleware stashed the URL the user was trying to reach
     * before being bounced to login (e.g. a "Request This Service"
     * link) — falls back to the dashboard if nothing was stashed.
     */
    private function intendedUrl(): string
    {
        $default = (Auth::hasRole('customer') && !Auth::can('users.view')) ? '/account' : '/dashboard';
        $url = Session::get('_intended_url', $default);
        Session::forget('_intended_url');

        return $url;
    }
}
