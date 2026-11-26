<?php

declare(strict_types=1);

namespace AlbaTech\Core;

/** One consistent WhatsApp destination and prefilled message policy for the entire site. */
final class WhatsAppLink
{
    /**
     * @param string|null $sourcePath Page path the chat starts from. Appended as "(From: /path)"
     *                                so the inbox can show which page brought the lead.
     */
    public static function build(array $settings, ?string $fallbackPath = '/get-help', ?string $sourcePath = null): string
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
        $sourcePath = $sourcePath !== null ? (parse_url($sourcePath, PHP_URL_PATH) ?: '') : '';
        if ($sourcePath !== '' && $sourcePath !== '/' && preg_match('~^/[a-z0-9/_-]{1,200}$~i', $sourcePath) === 1) {
            // Format must match WhatsAppPolicy::extractSourcePage(). Kept inline because some
            // tests load this file without the autoloader.
            $message .= "\n\n(From: " . $sourcePath . ')';
        }
        return 'https://wa.me/' . $number . '?text=' . rawurlencode($message);
    }
}
