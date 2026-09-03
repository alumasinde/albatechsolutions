<?php

declare(strict_types=1);

namespace App\Core;

abstract class BaseController
{
    protected function view(string $view, array $data = [], int $status = 200): Response
    {
        return Response::view($view, $data, $status);
    }

    protected function json(array $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    protected function redirect(string $url, int $status = 302): Response
    {
        return Response::redirect($url, $status);
    }

    protected function back(): Response
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';

        return Response::redirect($referer);
    }
}
