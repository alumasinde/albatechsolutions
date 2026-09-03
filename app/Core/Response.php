<?php

declare(strict_types=1);

namespace App\Core;

use JsonException;
use RuntimeException;

final class Response
{
    /**
     * @param array<string, string> $headers
     */
    private function __construct(
        private readonly string $content,
        private readonly int $status = 200,
        private readonly array $headers = []
    ) {
    }

    /**
     * Create an HTML response.
     *
     * @param array<string, string> $headers
     */
    public static function html(
        string $content,
        int $status = 200,
        array $headers = []
    ): self {
        return new self(
            $content,
            self::validateStatus($status),
            self::mergeHeaders(
                [
                    'Content-Type' => 'text/html; charset=UTF-8',
                ],
                $headers
            )
        );
    }

    /**
     * Create a JSON response.
     *
     * @param mixed $data
     * @param array<string, string> $headers
     */
    public static function json(
        mixed $data,
        int $status = 200,
        array $headers = []
    ): self {
        try {
            $content = json_encode(
                $data,
                JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
                | JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            throw new RuntimeException(
                'Unable to encode JSON response.',
                0,
                $exception
            );
        }

        return new self(
            $content,
            self::validateStatus($status),
            self::mergeHeaders(
                [
                    'Content-Type' => 'application/json; charset=UTF-8',
                    'Cache-Control' => 'no-store',
                ],
                $headers
            )
        );
    }

    /**
     * Create a plain-text response.
     *
     * @param array<string, string> $headers
     */
    public static function text(
        string $content,
        int $status = 200,
        array $headers = []
    ): self {
        return new self(
            $content,
            self::validateStatus($status),
            self::mergeHeaders(
                [
                    'Content-Type' => 'text/plain; charset=UTF-8',
                ],
                $headers
            )
        );
    }

    /**
     * Create a redirect response.
     *
     * 301 Permanent
     * 302 Found
     * 303 See Other
     * 307 Temporary Redirect
     * 308 Permanent Redirect
     *
     * @param array<string, string> $headers
     */
    public static function redirect(
        string $url,
        int $status = 302,
        array $headers = []
    ): self {
        $url = trim($url);

        if ($url === '') {
            throw new RuntimeException(
                'Redirect URL cannot be empty.'
            );
        }

        if (!filter_var($url, FILTER_VALIDATE_URL) && !str_starts_with($url, '/')) {
            throw new RuntimeException(
                'Redirect URL must be an absolute URL or a local path.'
            );
        }

        if (!in_array($status, [301, 302, 303, 307, 308], true)) {
            throw new RuntimeException(
                sprintf(
                    'Invalid redirect status code: %d',
                    $status
                )
            );
        }

        return new self(
            '',
            $status,
            self::mergeHeaders(
                [
                    'Location' => $url,
                ],
                $headers
            )
        );
    }

    /**
     * Render a view and return an HTML response.
     *
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     */
    public static function view(
        string $view,
        array $data = [],
        int $status = 200,
        array $headers = []
    ): self {
        return self::html(
            View::render($view, $data),
            $status,
            $headers
        );
    }

    /**
     * Create a binary/file response.
     *
     * Useful for generated PDFs and other non-HTML artifacts.
     */
    public static function binary(
        string $content,
        string $contentType,
        string $filename,
        int $status = 200,
        bool $inline = true
    ): self {
        $safeFilename = preg_replace('/[^A-Za-z0-9._-]+/', '-', basename($filename)) ?: 'download';
        $disposition = $inline ? 'inline' : 'attachment';

        return new self(
            $content,
            self::validateStatus($status),
            self::mergeHeaders([
                'Content-Type' => $contentType,
                'Content-Length' => (string) strlen($content),
                'Content-Disposition' => $disposition . '; filename="' . $safeFilename . '"',
                'Cache-Control' => 'private, no-store, max-age=0',
                'X-Content-Type-Options' => 'nosniff',
            ])
        );
    }

    /**
     * Create an empty response.
     *
     * Useful for 204 No Content.
     *
     * @param array<string, string> $headers
     */
    public static function empty(
        int $status = 204,
        array $headers = []
    ): self {
        $status = self::validateStatus($status);

        if ($status === 204 || $status === 304) {
            return new self('', $status, $headers);
        }

        return new self('', $status, $headers);
    }

    /**
     * Send the response.
     *
     * Security headers are applied centrally here.
     */
    public function send(): void
    {
        if (headers_sent($file, $line)) {
            throw new RuntimeException(
                sprintf(
                    'Cannot send response headers because output has already started in %s on line %d.',
                    $file,
                    $line
                )
            );
        }

        /*
         * SecurityHeaders owns application-wide security headers.
         *
         * This means Response does not need to know anything about:
         *
         * - CSP
         * - HSTS
         * - Permissions-Policy
         * - X-Frame-Options
         * - Referrer-Policy
         * - etc.
         */
        $securityHeaders = SecurityHeaders::all();

        /*
         * Security headers are authoritative.
         *
         * A controller cannot accidentally override the application's
         * global CSP or HSTS policy through a normal response header.
         */
        $headers = self::mergeHeaders(
            $this->headers,
            $securityHeaders
        );

        /*
         * A response with no content should not send a content type
         * inherited from another response type.
         */
        if (
            ($this->status === 204 || $this->status === 304)
            && isset($headers['content-type'])
        ) {
            unset($headers['content-type']);
        }

        http_response_code($this->status);

        foreach ($headers as $name => $value) {
            header(
                sprintf('%s: %s', $name, $value),
                true
            );
        }

        /*
         * 204 and 304 responses must not contain a response body.
         */
        if ($this->status === 204 || $this->status === 304) {
            return;
        }

        echo $this->content;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function content(): string
    {
        return $this->content;
    }

    /**
     * @return array<string, string>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Merge headers case-insensitively.
     *
     * SecurityHeaders are deliberately passed last by send()
     * so security policy cannot accidentally be weakened.
     *
     * @param array<string, string> ...$sets
     * @return array<string, string>
     */
    private static function mergeHeaders(
        array ...$sets
    ): array {
        $result = [];

        foreach ($sets as $set) {
            foreach ($set as $name => $value) {
                $normalizedName = self::normalizeHeaderName($name);

                $result[$normalizedName] = $value;
            }
        }

        return $result;
    }

    private static function normalizeHeaderName(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            throw new RuntimeException(
                'HTTP header name cannot be empty.'
            );
        }

        /*
         * Keep standard header casing for readable output.
         */
        return match (strtolower($name)) {
            'content-type' => 'Content-Type',
            'cache-control' => 'Cache-Control',
            'location' => 'Location',
            'content-disposition' => 'Content-Disposition',
            'content-language' => 'Content-Language',
            'content-encoding' => 'Content-Encoding',
            'content-security-policy' => 'Content-Security-Policy',
            'strict-transport-security' => 'Strict-Transport-Security',
            'x-content-type-options' => 'X-Content-Type-Options',
            'x-frame-options' => 'X-Frame-Options',
            'referrer-policy' => 'Referrer-Policy',
            'permissions-policy' => 'Permissions-Policy',
            'x-permitted-cross-domain-policies' => 'X-Permitted-Cross-Domain-Policies',
            'x-xss-protection' => 'X-XSS-Protection',
            default => $name,
        };
    }

    private static function validateStatus(int $status): int
    {
        if ($status < 100 || $status > 599) {
            throw new RuntimeException(
                sprintf(
                    'Invalid HTTP status code: %d',
                    $status
                )
            );
        }

        return $status;
    }
}