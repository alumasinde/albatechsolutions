<?php

declare(strict_types=1);

namespace AlbaTech\Core;

use Throwable;

final class Application
{
    private Router $router;
    private Request $request;

    public function __construct()
    {
        $this->router = new Router();
        $this->request = new Request();
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function run(): void
    {
        try {
            $this->router->dispatch($this->request);
        } catch (Throwable $exception) {
            $requestId = bin2hex(random_bytes(6));
            error_log(sprintf(
                '[AlbaTech][%s] %s in %s:%d',
                $requestId,
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            ));

            $debug = Env::bool('APP_DEBUG', false) && Env::get('APP_ENV', 'local') !== 'production';
            $message = $debug
                ? View::escape($exception->getMessage()) . ' (Reference: ' . $requestId . ')'
                : 'Something went wrong. Please try again later. Reference: ' . $requestId;

            Response::html(View::render('errors/500', [
                'pageTitle' => 'Something went wrong',
                'errorMessage' => $message,
            ]), 500);
        }
    }
}
