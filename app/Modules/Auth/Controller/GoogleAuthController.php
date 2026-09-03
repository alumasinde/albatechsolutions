<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controller;

use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Auth\Service\AuthService;
use App\Modules\Auth\Service\GoogleOAuthService;

final class GoogleAuthController extends BaseController
{
    private const SESSION_STATE_KEY = '_google_oauth_state';

    public function __construct(
        private readonly GoogleOAuthService $google,
        private readonly AuthService $authService
    ) {
    }

    public function start(Request $request): Response
    {
        $state = bin2hex(random_bytes(16));
        Session::put(self::SESSION_STATE_KEY, $state);
        return Response::redirect($this->google->authorizationUrl($state));
    }

    public function callback(Request $request): Response
    {
        $expectedState = Session::get(self::SESSION_STATE_KEY);
        Session::forget(self::SESSION_STATE_KEY);
        $state = (string) $request->input('state', '');
        $code = (string) $request->input('code', '');

        if (!$expectedState || $state !== $expectedState) {
            Session::flash('_errors', ['google' => ['Sign-in session expired or invalid. Please try again.']]);
            return $this->redirect(Config::get('auth.login_path', '/login'));
        }
        if ($request->input('error') || !$code) {
            Session::flash('_errors', ['google' => ['Google sign-in was cancelled or failed.']]);
            return $this->redirect(Config::get('auth.login_path', '/login'));
        }

        $profile = $this->google->handleCallback($code);
        if (!$profile) {
            Session::flash('_errors', ['google' => ['Could not verify your Google account. Please try again.']]);
            return $this->redirect(Config::get('auth.login_path', '/login'));
        }

        $result = $this->authService->loginOrRegisterViaGoogle($profile);
        if (!$result['success']) {
            Session::flash('_errors', ['google' => [$result['message']]]);
            return $this->redirect(Config::get('auth.login_path', '/login'));
        }

        if (!empty($result['requires_2fa'])) {
            return $this->redirect(Config::get('auth.login_path', '/login') . '/verify');
        }

        $intended = Session::get('_intended_url', '/dashboard');
        Session::forget('_intended_url');
        return $this->redirect($intended);
    }
}
