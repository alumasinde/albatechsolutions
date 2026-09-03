<?php

declare(strict_types=1);

namespace App\Modules\Admin\Controller;

use App\Core\AuditLog;
use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Config;
use App\Core\Helpers\Totp;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Auth\Repository\UserRepository;

final class SecurityController extends BaseController
{
    private const SESSION_PENDING_SECRET = '_2fa_setup_pending_secret';

    public function __construct(
        private readonly UserRepository $users
    ) {
    }

    public function show(Request $request): Response
    {
        $user = Auth::user();
        $pendingSecret = Session::get(self::SESSION_PENDING_SECRET);

        $setupUri = null;
        if ($pendingSecret && empty($user['two_factor_enabled'])) {
            $setupUri = Totp::provisioningUri($pendingSecret, $user['email'], Config::get('app.name', 'AlbaTech Solutions'));
        }

        return $this->view('admin.security.two-factor', [
            'user' => $user,
            'setupUri' => $setupUri,
            'setupSecret' => $pendingSecret,
        ]);
    }

    /**
     * Generates a new pending secret, stores it in session, then
     * redirects to the GET show() page to render it — never renders
     * directly from a POST handler, so the resulting page is always
     * safe to refresh without re-triggering the action.
     */
    public function startSetup(Request $request): Response
    {
        Session::put(self::SESSION_PENDING_SECRET, Totp::generateSecret());

        return $this->redirect(Config::get('admin.path', '/admin') . '/security/2fa');
    }

    public function confirmSetup(Request $request): Response
    {
        $secret = Session::get(self::SESSION_PENDING_SECRET);
        $code = (string) $request->input('code', '');

        if (!$secret || !Totp::verify($secret, $code)) {
            Session::flash('_errors', ['code' => ['Invalid code. Please try again.']]);

            return $this->back();
        }

        $recoveryCodes = Totp::generateRecoveryCodes();

        $this->users->update((int) Auth::id(), [
            'two_factor_secret'         => $secret,
            'two_factor_enabled'        => 1,
            'two_factor_recovery_codes' => json_encode($recoveryCodes),
            'two_factor_confirmed_at'   => date('Y-m-d H:i:s'),
        ]);

        Session::forget(self::SESSION_PENDING_SECRET);
        Session::flash('_recovery_codes', $recoveryCodes);
        AuditLog::record('2fa.enabled', 'user', Auth::id());

        return $this->redirect(Config::get('admin.path', '/admin') . '/security/2fa/recovery-codes');
    }

    public function showRecoveryCodes(Request $request): Response
    {
        $codes = Session::getFlash('_recovery_codes');

        if (!$codes) {
            return $this->redirect(Config::get('admin.path', '/admin') . '/security/2fa');
        }

        return $this->view('admin.security.recovery-codes', ['codes' => $codes]);
    }

    public function disable(Request $request): Response
    {
        $user = Auth::user();

        if (!password_verify((string) $request->input('password', ''), $user['password'])) {
            Session::flash('_errors', ['password' => ['Incorrect password.']]);

            return $this->back();
        }

        $this->users->update((int) Auth::id(), [
            'two_factor_secret'         => null,
            'two_factor_enabled'        => 0,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at'   => null,
        ]);

        AuditLog::record('2fa.disabled', 'user', Auth::id());
        Session::flash('_success', 'Two-factor authentication disabled.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/security/2fa');
    }
}
