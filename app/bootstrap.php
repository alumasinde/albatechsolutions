<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Container;
use App\Core\Logger;
use App\Core\Monitoring\SentryReporter;
use App\Core\Session;

require dirname(__DIR__) . '/vendor/autoload.php';

// --- Environment ---------------------------------------------------
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

// Fail closed for production secrets. AlbaTech encrypts private customer
// tokens with APP_KEY, so running production with an empty/weak key is unsafe.
$appEnv = (string) ($_ENV['APP_ENV'] ?? 'production');
$appDebug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
$appKey = (string) ($_ENV['APP_KEY'] ?? '');
$appUrl = (string) ($_ENV['APP_URL'] ?? '');
if ($appEnv === 'production') {
    $configurationError = null;
    if ($appDebug) {
        $configurationError = 'Production configuration error.';
    } elseif (strlen($appKey) < 32) {
        $configurationError = 'Production configuration error.';
    } elseif (!str_starts_with(strtolower($appUrl), 'https://')) {
        $configurationError = 'Production configuration error.';
    }

    if ($configurationError !== null) {
        ini_set('display_errors', '0');
        http_response_code(503);
        header('Content-Type: text/plain; charset=UTF-8');
        header('Cache-Control: no-store, max-age=0');
        echo $configurationError;
        exit;
    }
}

// --- Error monitoring ------------------------------------------------
SentryReporter::initialize();

// --- Error handling --------------------------------------------------
$debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
ini_set('display_errors', $debug ? '1' : '0');
error_reporting(E_ALL);

set_exception_handler(function (Throwable $e) use ($debug) {
    SentryReporter::report($e);

    Logger::error($e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);

    http_response_code(500);

    if ($debug) {
        echo '<pre>' . htmlspecialchars((string) $e) . '</pre>';
    } else {
        echo 'Something went wrong. Our team has been notified.';
    }
});

set_error_handler(function (int $severity, string $message, string $file, int $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    // Only escalate real errors to a thrown exception. Warnings/notices/
    // deprecations are logged and execution continues — a missing
    // optional form field, for example, should not 500 the request.
    $fatalSeverities = E_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR | E_CORE_ERROR | E_COMPILE_ERROR;

    if ($severity & $fatalSeverities) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    Logger::warning($message, ['file' => $file, 'line' => $line, 'severity' => $severity]);

    return true;
});

// PHP cannot route E_ERROR/E_CORE_ERROR/E_COMPILE_ERROR through the normal
// error handler. Capture those fatal errors before PHP terminates.
register_shutdown_function(function (): void {
    $error = error_get_last();

    if (!is_array($error)) {
        return;
    }

    $fatalSeverities = E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR;

    if (((int) ($error['type'] ?? 0) & $fatalSeverities) !== 0) {
        SentryReporter::reportFatal($error);
    }
});

// --- Timezone ----------------------------------------------------------
date_default_timezone_set(Config::get('app.timezone', 'Africa/Nairobi'));

// --- Container + Session -----------------------------------------------
Container::boot();
Session::start();
