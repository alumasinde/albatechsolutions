<?php

declare(strict_types=1);

namespace AlbaTech\Core;

/** One consistent WhatsApp destination and prefilled message policy for the entire site. */
final class WhatsAppLink
{
    public static function build(array $settings, ?string $fallbackPath = '/get-help'): string
    {
        $number = preg_replace('/\D+/', '', (string) ($settings['whatsapp_number'] ?? '')) ?? '';
        if (strlen($number) < 8 || strlen($number) > 15 || str_starts_with($number, '0')) {
            return $fallbackPath ?? '/get-help';
        }

        $siteName = trim((string) ($settings['site_name'] ?? 'AlbaTech Solutions')) ?: 'AlbaTech Solutions';
        $message = trim((string) ($settings['whatsapp_prefill_message'] ?? ''));
        if ($message === '') {
            $message = 'Hi ' . $siteName . ', I would like to make an enquiry. Please guide me on the next steps.';
        }
        return 'https://wa.me/' . $number . '?text=' . rawurlencode($message);
    }
}
