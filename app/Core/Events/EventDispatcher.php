<?php

declare(strict_types=1);

namespace App\Core\Events;

/**
 * EventDispatcher - lightweight synchronous pub/sub.
 *
 * Example: a service fires a domain event, and both the
 * InvoiceGenerator and NotificationService listen for it, so payment
 * logic doesn't need to know about invoicing or notifications.
 */
final class EventDispatcher
{
    private static array $listeners = [];

    public static function listen(string $event, callable $listener): void
    {
        self::$listeners[$event][] = $listener;
    }

    public static function dispatch(string $event, mixed $payload = null): void
    {
        foreach (self::$listeners[$event] ?? [] as $listener) {
            $listener($payload);
        }
    }
}
