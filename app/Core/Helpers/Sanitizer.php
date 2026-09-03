<?php

declare(strict_types=1);

namespace App\Core\Helpers;

final class Sanitizer
{
    /**
     * Escape a value for safe HTML output. Use this (or the global e()
     * helper) around every piece of user- or DB-sourced data echoed
     * into a view.
     */
    public static function escape(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function stripTags(string $value): string
    {
        return trim(strip_tags($value));
    }

    /**
     * Whitelist-based rich text cleanup for CMS/blog body content.
     * Strips script/style/event-handler vectors while allowing a
     * limited set of formatting tags.
     */
    public static function cleanRichText(string $html): string
    {
        $allowed = '<p><br><strong><em><ul><ol><li><a><h2><h3><h4><blockquote><img>';
        $clean = strip_tags($html, $allowed);

        // Strip on* event handler attributes and javascript: URIs.
        $clean = preg_replace('/on\w+\s*=\s*("[^"]*"|\'[^\']*\')/i', '', $clean);
        $clean = preg_replace('/javascript\s*:/i', '', $clean);

        return $clean;
    }

    public static function slug(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);

        return trim($value, '-');
    }
}
