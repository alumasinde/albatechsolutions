<?php

declare(strict_types=1);

namespace App\Modules\Auth\Service;

use App\Core\Auth;
use App\Core\AuditLog;
use App\Core\BaseService;
use App\Core\Config;
use App\Core\Helpers\Totp;
use App\Core\Notifications\NotificationService;
use App\Modules\Auth\Repository\UserRepository;
use App\Modules\Auth\Repository\UserTokenRepository;

final class AuthService extends BaseService
{
    /**
     * Prefer Argon2id for newly-created passwords when the PHP build
     * supports it. Existing bcrypt hashes remain valid via password_verify().
     */
    private static function hashPassword(string $password): string
    {
        $algorithm = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
        return password_hash($password, $algorithm);
    }

    private const MAX_FAILED_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;
    private const PASSWORD_RESET_TTL_MINUTES = 60;
    private const EMAIL_VERIFICATION_TTL_MINUTES = 60 * 24;

    public function __construct(
        private readonly UserRepository $users,
        private readonly UserTokenRepository $tokens,
        private readonly NotificationService $notifications
    ) {
    }

    /**
     * @return array{success: bool, message?: string}
     */
    public function register(array $data): array
    {
        if ($this->users->findByEmail($data['email'])) {
            return ['success' => false, 'message' => 'An account with this email already exists.'];
        }

        $userId = $this->users->create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'phone'     => $data['phone'] ?? null,
            'password'  => self::hashPassword($data['password']),
            'is_active' => 1,
        ]);

        $roleStmt = \App\Core\Database::connection()->prepare('SELECT id FROM roles WHERE slug = :slug LIMIT 1');
        $roleStmt->execute(['slug' => 'customer']);
        $roleId = $roleStmt->fetchColumn();

        if ($roleId) {
            \App\Core\Database::connection()
                ->prepare('INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)')
                ->execute(['user_id' => $userId, 'role_id' => $roleId]);
        }

        Auth::login($userId);
        AuditLog::record('customer.registered', 'user', $userId);
        $this->sendEmailVerification($userId, $data['email'], $data['name']);

        return ['success' => true];
    }

    /**
     * @return array{success: bool, message?: string}
     */
    public function attemptLogin(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);

        if (!$user) {
            // Don't reveal whether the email exists.
            return ['success' => false, 'message' => 'Invalid credentials.'];
        }

        if ($this->isLockedOut($user)) {
            return ['success' => false, 'message' => 'Account temporarily locked due to repeated failed attempts. Try again later.'];
        }

        if (empty($user['password'])) {
            return ['success' => false, 'message' => 'This account uses Google Sign-In. Please use the "Sign in with Google" button.'];
        }

        if (!password_verify($password, $user['password'])) {
            $this->users->recordFailedLogin((int) $user['id']);
            AuditLog::record('login.failed', 'user', (int) $user['id']);

            return ['success' => false, 'message' => 'Invalid credentials.'];
        }

        if (!empty($user['is_active']) === false) {
            return ['success' => false, 'message' => 'This account has been deactivated.'];
        }

        $this->users->resetFailedLogins((int) $user['id']);

        if (!empty($user['two_factor_enabled'])) {
            Auth::markPendingTwoFactor((int) $user['id']);
            AuditLog::record('login.password_verified_awaiting_2fa', 'user', (int) $user['id']);

            return ['success' => true, 'requires_2fa' => true];
        }

        Auth::login((int) $user['id']);
        AuditLog::record('login.success', 'user', (int) $user['id']);

        return ['success' => true];
    }

    /**
     * @return array{success: bool, message?: string}
     */
    public function verifyTwoFactorChallenge(string $code): array
    {
        $userId = Auth::pendingTwoFactorUserId();

        if ($userId === null) {
            return ['success' => false, 'message' => 'No pending login to verify.'];
        }

        $user = $this->users->find($userId);

        if (!$user) {
            return ['success' => false, 'message' => 'No pending login to verify.'];
        }

        if (Totp::verify($user['two_factor_secret'], $code)) {
            Auth::confirmTwoFactor();
            AuditLog::record('login.2fa_success', 'user', $userId);

            return ['success' => true];
        }

        if ($this->consumeRecoveryCode($user, $code)) {
            Auth::confirmTwoFactor();
            AuditLog::record('login.2fa_recovery_code_used', 'user', $userId);

            return ['success' => true];
        }

        AuditLog::record('login.2fa_failed', 'user', $userId);

        return ['success' => false, 'message' => 'Invalid or expired code.'];
    }

    private function consumeRecoveryCode(array $user, string $submitted): bool
    {
        $codes = json_decode((string) ($user['two_factor_recovery_codes'] ?? '[]'), true) ?: [];
        $submitted = strtoupper(trim($submitted));

        $index = array_search($submitted, $codes, true);

        if ($index === false) {
            return false;
        }

        unset($codes[$index]);
        $this->users->update((int) $user['id'], [
            'two_factor_recovery_codes' => json_encode(array_values($codes)),
        ]);

        return true;
    }

    /**
     * @param array{email: string, name: string, google_id: string} $profile
     * @return array{success: bool, message?: string}
     */
    public function loginOrRegisterViaGoogle(array $profile): array
    {
        $user = $this->users->findByGoogleId($profile['google_id']);

        if ($user) {
            Auth::login((int) $user['id']);
            AuditLog::record('login.google', 'user', (int) $user['id']);

            return ['success' => true];
        }

        $user = $this->users->findByEmail($profile['email']);

        if ($user) {
            // Google Sign-In is customer-only — an existing staff/admin
            // account must keep using password (+2FA) login, not a
            // third-party OAuth flow that wasn't part of that review.
            if (!$this->isCustomerOnly((int) $user['id'])) {
                return ['success' => false, 'message' => 'This email belongs to a staff account. Please use the regular login form.'];
            }

            $this->users->linkGoogleId((int) $user['id'], $profile['google_id']);
            Auth::login((int) $user['id']);
            AuditLog::record('login.google_linked', 'user', (int) $user['id']);

            return ['success' => true];
        }

        $userId = $this->users->create([
            'name'      => $profile['name'],
            'email'     => $profile['email'],
            'password'  => null,
            'google_id' => $profile['google_id'],
            'is_active' => 1,
            'email_verified_at' => date('Y-m-d H:i:s'),
        ]);

        $roleStmt = \App\Core\Database::connection()->prepare('SELECT id FROM roles WHERE slug = :slug LIMIT 1');
        $roleStmt->execute(['slug' => 'customer']);
        $roleId = $roleStmt->fetchColumn();

        if ($roleId) {
            \App\Core\Database::connection()
                ->prepare('INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)')
                ->execute(['user_id' => $userId, 'role_id' => $roleId]);
        }

        Auth::login($userId);
        AuditLog::record('customer.registered_via_google', 'user', $userId);

        return ['success' => true];
    }

    private function isCustomerOnly(int $userId): bool
    {
        $stmt = \App\Core\Database::connection()->prepare(
            'SELECT COUNT(*) FROM user_roles ur
             INNER JOIN roles r ON r.id = ur.role_id
             WHERE ur.user_id = :user_id AND r.slug != :customer_slug'
        );
        $stmt->execute(['user_id' => $userId, 'customer_slug' => 'customer']);

        return (int) $stmt->fetchColumn() === 0;
    }

    /**
     * Always returns success (even for an unknown email) so the response
     * never reveals whether an account exists — the actual email is only
     * sent when it does.
     */
    public function requestPasswordReset(string $email): array
    {
        $user = $this->users->findByEmail($email);

        if ($user && !empty($user['password'])) {
            $raw = $this->tokens->issue((int) $user['id'], 'password_reset', self::PASSWORD_RESET_TTL_MINUTES);
            $link = rtrim((string) Config::get('app.url'), '/') . '/reset-password/' . $raw;

            $this->notifications->notify(
                $user['email'],
                'Reset your password',
                "Hi {$user['name']},\n\nA password reset was requested for your account. If this was you, use the link below within the next hour:\n\n{$link}\n\nIf you didn't request this, you can safely ignore this email."
            );

            AuditLog::record('password_reset.requested', 'user', (int) $user['id']);
        }

        return ['success' => true];
    }

    public function resetPassword(string $rawToken, string $newPassword): array
    {
        $tokenRow = $this->tokens->findValid($rawToken, 'password_reset');

        if (!$tokenRow) {
            return ['success' => false, 'message' => 'This reset link is invalid or has expired. Please request a new one.'];
        }

        $this->users->update((int) $tokenRow['user_id'], [
            'password' => self::hashPassword($newPassword),
            'failed_login_attempts' => 0,
        ]);
        $this->tokens->consume((int) $tokenRow['id']);

        AuditLog::record('password_reset.completed', 'user', (int) $tokenRow['user_id']);

        return ['success' => true];
    }

    public function sendEmailVerification(int $userId, string $email, string $name): void
    {
        $raw = $this->tokens->issue($userId, 'email_verification', self::EMAIL_VERIFICATION_TTL_MINUTES);
        $link = rtrim((string) Config::get('app.url'), '/') . '/verify-email/' . $raw;

        $this->notifications->notify(
            $email,
            'Verify your email address',
            "Hi {$name},\n\nPlease confirm your email address by clicking the link below:\n\n{$link}\n\nThis link expires in 24 hours."
        );
    }

    public function resendEmailVerification(int $userId): array
    {
        $user = $this->users->find($userId);

        if (!$user) {
            return ['success' => false, 'message' => 'Account not found.'];
        }

        if (!empty($user['email_verified_at'])) {
            return ['success' => true, 'message' => 'Your email is already verified.'];
        }

        $this->sendEmailVerification($userId, $user['email'], $user['name']);

        return ['success' => true, 'message' => 'Verification email sent.'];
    }

    public function verifyEmail(string $rawToken): array
    {
        $tokenRow = $this->tokens->findValid($rawToken, 'email_verification');

        if (!$tokenRow) {
            return ['success' => false, 'message' => 'This verification link is invalid or has expired.'];
        }

        $this->users->update((int) $tokenRow['user_id'], ['email_verified_at' => date('Y-m-d H:i:s')]);
        $this->tokens->consume((int) $tokenRow['id']);

        AuditLog::record('email.verified', 'user', (int) $tokenRow['user_id']);

        return ['success' => true];
    }

    private function isLockedOut(array $user): bool
    {
        if ((int) ($user['failed_login_attempts'] ?? 0) < self::MAX_FAILED_ATTEMPTS) {
            return false;
        }

        if (empty($user['last_failed_login_at'])) {
            return false;
        }

        $lockedUntil = strtotime($user['last_failed_login_at']) + (self::LOCKOUT_MINUTES * 60);

        return time() < $lockedUntil;
    }
}
