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
            $debug = Env::bool('APP_DEBUG', false);

            error_log(sprintf(
                '[AlbaTech] %s in %s:%d',
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            ));

            $message = $debug
                ? View::escape($exception->getMessage())
                : 'Something went wrong. Please try again later.';

            Response::html(View::render('errors/500', [
                'pageTitle' => 'Something went wrong',
                'errorMessage' => $message,
            ]), 500);
        }
    }
}
