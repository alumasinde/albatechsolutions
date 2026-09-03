<?php

declare(strict_types=1);

namespace App\Core\Monitoring;

use App\Core\Config;
use Throwable;

/**
 * Small application-level adapter around the Sentry PHP SDK.
 *
 * Sentry is optional: if SENTRY_DSN is empty or the SDK is not installed,
 * the application continues to use its normal local logger without failing.
 */
final class SentryReporter
{
    private static bool $initialized = false;
    private static bool $reported = false;

    public static function initialize(): void
    {
        if (self::$initialized) {
            return;
        }

        self::$initialized = true;

        $config = Config::get('sentry', []);
        $dsn = trim((string) ($config['dsn'] ?? ''));

        if ($dsn === '' || !class_exists('Sentry\\SentrySdk')) {
            return;
        }

        try {
            \Sentry\init([
                'dsn' => $dsn,
                'environment' => (string) ($config['environment'] ?? 'production'),
                'release' => (string) ($config['release'] ?? '') ?: null,
                'traces_sample_rate' => (float) ($config['traces_sample_rate'] ?? 0.0),
                'send_default_pii' => (bool) ($config['send_default_pii'] ?? false),
            ]);

            self::configureScope();
        } catch (Throwable $e) {
            // Monitoring must never become a reason the application fails.
            self::$initialized = false;
        }
    }

    public static function report(Throwable $exception, array $context = []): void
    {
        self::initialize();

        if (self::$reported || !self::available()) {
            return;
        }

        self::$reported = true;

        try {
            \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($exception, $context): void {
                self::addRequestContext($scope);

                foreach ($context as $key => $value) {
                    if (is_scalar($value) || $value === null) {
                        $scope->setExtra((string) $key, $value);
                    }
                }

                \Sentry\captureException($exception);
            });
        } catch (Throwable) {
            // Never allow monitoring failures to replace the original error.
        }
    }

    public static function reportFatal(array $error): void
    {
        if (self::$reported || ($error['message'] ?? '') === '') {
            return;
        }

        $exception = new \ErrorException(
            (string) $error['message'],
            0,
            (int) ($error['type'] ?? E_ERROR),
            (string) ($error['file'] ?? 'unknown'),
            (int) ($error['line'] ?? 0)
        );

        self::report($exception, ['fatal' => true]);
    }

    private static function available(): bool
    {
        return class_exists('Sentry\\SentrySdk') && function_exists('Sentry\\captureException');
    }

    private static function configureScope(): void
    {
        if (!self::available()) {
            return;
        }

        try {
            \Sentry\configureScope(function (\Sentry\State\Scope $scope): void {
                $scope->setTag('application', 'albatechsolutions');
                $scope->setTag('php_version', PHP_VERSION);

                $environment = (string) Config::get('app.env', 'production');
                $scope->setTag('app_environment', $environment);
            });
        } catch (Throwable) {
            // Monitoring setup is intentionally best-effort.
        }
    }

    private static function addRequestContext(\Sentry\State\Scope $scope): void
    {
        $scope->setTag('http_method', strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')));
        $scope->setTag('route_path', self::requestPath());

        // Do not send query strings, form fields, cookies, session IDs,
        // authorization headers or other potentially sensitive request data.
    }

    private static function requestPath(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        return is_string($path) && $path !== '' ? $path : '/';
    }
}
