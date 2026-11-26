<?php

declare(strict_types=1);

namespace AlbaTech\Core;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Pure WhatsApp rules with no I/O, so they can be unit tested:
 * webhook signatures, delivery-status ordering, retry decisions, business hours,
 * opt-out keywords and the "(From: /page)" source tag added to wa.me links.
 */
final class WhatsAppPolicy
{
    public const MAX_SEND_ATTEMPTS = 5;
    public const MAX_EVENT_ATTEMPTS = 3;
    public const OPT_OUT_PATTERN = '/^\s*(stop|unsubscribe|opt[ -]?out|remove me|acha|niondokee)\b/i';
    public const OPT_IN_PATTERN = '/^\s*(start|unstop|subscribe|opt[ -]?in)\b/i';

    /** Meta error codes that are temporary (rate limits, server trouble). */
    private const RETRYABLE_CODES = ['1', '2', '4', '17', '80007', '130429', '131000', '131016', '131056', '133004'];

    private const DAYS = ['mon' => 1, 'tue' => 2, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6, 'sun' => 7];

    /** Meta signs every webhook body with the app secret. An empty secret never validates. */
    public static function signatureValid(string $rawBody, string $secret, string $header): bool
    {
        if ($secret === '' || $header === '') {
            return false;
        }
        return hash_equals('sha256=' . hash_hmac('sha256', $rawBody, $secret), $header);
    }

    /** Whether a status update may replace the stored one (statuses can arrive out of order). */
    public static function statusMayReplace(string $current, string $incoming): bool
    {
        if ($incoming === 'failed') {
            return $current !== 'read' && $current !== 'delivered';
        }
        return self::statusRank($incoming) > self::statusRank($current);
    }

    public static function statusRank(string $status): int
    {
        return match ($status) {
            'sent' => 1,
            'delivered' => 2,
            'read' => 3,
            default => 0,
        };
    }

    public static function isRetryable(int $httpStatus, string $errorCode): bool
    {
        if ($httpStatus === 0 || $httpStatus === 429 || $httpStatus >= 500) {
            return true;
        }
        return in_array($errorCode, self::RETRYABLE_CODES, true);
    }

    /** Seconds to wait before retry number $attempt (1-based). */
    public static function backoffSeconds(int $attempt): int
    {
        return [1 => 60, 2 => 300, 3 => 900, 4 => 3600][$attempt] ?? 21600;
    }

    /**
     * Business hours like "Mon-Fri 08:00-18:00; Sat 09:00-13:00" (Nairobi time).
     * An empty spec means "always open". Invalid parts are ignored.
     */
    public static function isOpen(string $spec, ?DateTimeImmutable $at = null): bool
    {
        $rules = self::parseHours($spec);
        if ($rules === null) {
            return true;
        }
        $at = ($at ?? new DateTimeImmutable('now'))->setTimezone(new DateTimeZone('Africa/Nairobi'));
        $day = (int) $at->format('N');
        $minutes = (int) $at->format('G') * 60 + (int) $at->format('i');
        foreach ($rules as [$days, $open, $close]) {
            if (in_array($day, $days, true) && $minutes >= $open && $minutes < $close) {
                return true;
            }
        }
        return false;
    }

    /** @return list<array{0: list<int>, 1: int, 2: int}>|null null when the spec is empty */
    public static function parseHours(string $spec): ?array
    {
        $spec = trim($spec);
        if ($spec === '') {
            return null;
        }
        $rules = [];
        foreach (preg_split('/[;\n]+/', $spec) ?: [] as $part) {
            if (preg_match('/^\s*([a-z]{3})(?:\s*-\s*([a-z]{3}))?\s+(\d{1,2}):(\d{2})\s*-\s*(\d{1,2}):(\d{2})\s*$/i', $part, $m) !== 1) {
                continue;
            }
            $from = self::DAYS[strtolower($m[1])] ?? null;
            $to = $m[2] !== '' ? (self::DAYS[strtolower($m[2])] ?? null) : $from;
            if ($from === null || $to === null || $to < $from) {
                continue;
            }
            $open = (int) $m[3] * 60 + (int) $m[4];
            $close = (int) $m[5] * 60 + (int) $m[6];
            if ($close <= $open || $close > 1440) {
                continue;
            }
            $rules[] = [range($from, $to), $open, $close];
        }
        return $rules;
    }

    /** Reads the "(From: /path)" tag WhatsAppLink::build() appends to wa.me prefill text. */
    public static function extractSourcePage(string $text): ?string
    {
        return preg_match('~\(From:\s*(/[a-z0-9/_-]*)\)~i', $text, $m) === 1 ? substr($m[1], 0, 255) : null;
    }

    public static function isOptOut(string $text): bool
    {
        return preg_match(self::OPT_OUT_PATTERN, $text) === 1;
    }

    public static function isOptIn(string $text): bool
    {
        return preg_match(self::OPT_IN_PATTERN, $text) === 1;
    }

    /**
     * Topic for free-text messages when the customer types instead of tapping a menu button.
     * @return array{topic: string, line: string, path: string}|null
     */
    public static function keywordTopic(string $text): ?array
    {
        $map = [
            ['/\b(kra|tax|returns?|etims|tcc|itax)\b/i', 'compliance', 'We help with KRA returns, PIN and eTIMS. Prices and details:', '/services/kra-ecitizen-help'],
            ['/\b(sha|shif|nhif)\b/i', 'compliance', 'We help with SHA registration:', '/services/sha-registration-help-in-kenya'],
            ['/\b(ntsa|driving licen[cs]e|smart ?dl)\b/i', 'compliance', 'We help with NTSA and Smart DL services:', '/services/ntsa-smart-dl-assistance'],
            ['/\b(ecitizen|passport|good conduct)\b/i', 'compliance', 'We help with eCitizen applications:', '/services/ecitizen-application-assistance'],
            ['/\b(cr12|brs|business name|register (a |my )?(business|company))\b/i', 'build', 'Register, get compliant and get online with our SME Launchpad:', '/services/sme-launchpad'],
            ['/\b(website|web ?site|web ?design|landing page|domain|hosting)\b/i', 'build', 'We design business websites:', '/services/website-design'],
            ['/\b(pos|inventory|booking|rental|software|system|app)\b/i', 'build', 'We build business systems:', '/solutions'],
            ['/\b(price|pricing|cost|how much|bei|charges?)\b/i', 'general', 'Our services and prices are listed here:', '/services'],
            ['/\b(location|address|office|directions?|where are you)\b/i', 'general', 'Our location and contact details:', '/contact-details'],
        ];
        foreach ($map as [$pattern, $topic, $line, $path]) {
            if (preg_match($pattern, $text) === 1) {
                return ['topic' => $topic, 'line' => $line, 'path' => $path];
            }
        }
        return null;
    }
}
