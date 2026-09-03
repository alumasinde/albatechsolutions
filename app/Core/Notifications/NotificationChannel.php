<?php

declare(strict_types=1);

namespace App\Core\Notifications;

interface NotificationChannel
{
    public function name(): string;

    public function available(): bool;

    public function send(NotificationMessage $message): NotificationResult;
}
