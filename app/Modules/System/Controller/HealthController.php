<?php

declare(strict_types=1);

namespace App\Modules\System\Controller;

use App\Core\Config;
use App\Core\Database;
use App\Core\Response;

final class HealthController
{
    public function live(): Response
    {
        return Response::json([
            'status' => 'ok',
            'service' => Config::get('app.name', 'AlbaTech Solutions'),
        ], 200, [
            'Cache-Control' => 'no-store, max-age=0',
        ]);
    }

    public function ready(): Response
    {
        $checks = [
            'app_key' => strlen((string) Config::get('app.key', '')) >= 32,
            'production_debug_off' => Config::get('app.env', 'production') !== 'production' || Config::get('app.debug', false) === false,
            'database' => false,
        ];

        try {
            Database::connection()->query('SELECT 1');
            $checks['database'] = true;
        } catch (\Throwable) {
            $checks['database'] = false;
        }

        $ready = !in_array(false, $checks, true);

        return Response::json([
            'status' => $ready ? 'ready' : 'not_ready',
            'checks' => array_map(static fn (bool $value): string => $value ? 'ok' : 'failed', $checks),
        ], $ready ? 200 : 503, [
            'Cache-Control' => 'no-store, max-age=0',
        ]);
    }
}
