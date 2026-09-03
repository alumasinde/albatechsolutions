<?php

declare(strict_types=1);

namespace App\Core\Notifications;

final class NotificationMessage
{
    public function __construct(
        public readonly string $channel,
        public readonly string $recipient,
        public readonly string $subject,
        public readonly string $body,
        public readonly array $data = [],
        public readonly ?string $template = null,
        public readonly ?string $language = null,
    ) {}
}
