<?php

declare(strict_types=1);

namespace App\Core\Notifications;

final class NotificationService
{
    /** @var array<string, NotificationChannel> */
    private array $channels;

    public function __construct()
    {
        $channels = [
            new EmailNotificationChannel(),
            new SmsNotificationChannel(),
            new WhatsAppNotificationChannel(),
        ];
        $this->channels = [];
        foreach ($channels as $channel) $this->channels[$channel->name()] = $channel;
    }

    public function send(NotificationMessage $message): NotificationResult
    {
        $channel = $this->channels[$message->channel] ?? null;
        if (!$channel) return NotificationResult::failure('Unknown notification channel.');
        if (!$channel->available()) return NotificationResult::failure(ucfirst($message->channel) . ' channel is not configured.');
        return $channel->send($message);
    }

    /** @return string[] */
    public function availableChannels(): array
    {
        return array_values(array_filter(array_keys($this->channels), fn(string $name): bool => $this->channels[$name]->available()));
    }
}
