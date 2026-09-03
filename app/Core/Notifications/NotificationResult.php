<?php

declare(strict_types=1);

namespace App\Core\Notifications;

final class NotificationResult
{
    public function __construct(
        public readonly bool $accepted,
        public readonly ?string $messageId = null,
        public readonly ?string $error = null,
    ) {}

    public static function success(?string $messageId = null): self
    {
        return new self(true, $messageId);
    }

    public static function failure(string $error): self
    {
        return new self(false, null, $error);
    }
}
