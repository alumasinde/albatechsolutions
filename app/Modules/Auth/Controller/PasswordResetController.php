<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controller;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Helpers\Validator;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Auth\Service\AuthService;

final class PasswordResetController extends BaseController
{
    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    public function showForgotForm(Request $request): Response
    {
        if (Auth::check()) {
            return $this->redirect('/dashboard');
        }

        return $this->view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): Response
    {
        $validator = new Validator($request->all(), ['email' => 'required|email']);

        if ($validator->fails()) {
            Session::flash('_errors', $validator->errors());
            return $this->back();
        }

        $this->authService->requestPasswordReset((string) $request->input('email'));

        // Same message regardless of whether the email exists — never
        // confirm or deny an account to an unauthenticated visitor.
        Session::flash('_success', 'If an account exists for that email, a reset link has been sent.');

        return $this->redirect('/forgot-password');
    }

    public function showResetForm(Request $request): Response
    {
        if (Auth::check()) {
            return $this->redirect('/dashboard');
        }

        return $this->view('auth.reset-password', ['token' => (string) $request->param('token')]);
    }

    public function resetPassword(Request $request): Response
    {
        $validator = new Validator($request->all(), ['password' => 'required|min:8|confirmed']);

        if ($validator->fails()) {
            Session::flash('_errors', $validator->errors());
            return $this->back();
        }

        $token = (string) $request->input('token', '');
        $result = $this->authService->resetPassword($token, (string) $request->input('password'));

        if (!$result['success']) {
            Session::flash('_errors', ['token' => [$result['message']]]);
            return $this->back();
        }

        Session::flash('_success', 'Your password has been reset. Please sign in.');

        return $this->redirect(\App\Core\Config::get('auth.login_path', '/login'));
    }
}
