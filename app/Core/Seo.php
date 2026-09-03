<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Seo - reusable helpers for dynamic <title>, meta description,
 * canonical URL, Open Graph, Twitter Card, and JSON-LD structured
 * data. Every public view sets a few variables (title, metaDescription,
 * etc.) before requiring the layout; this class turns those into the
 * actual tags, and provides builders for the common schema.org types
 * so no view has to hand-write JSON-LD.
 */
final class Seo
{
    /**
     * Renders the full <head> SEO block: title, meta description,
     * canonical, OG tags, Twitter Card tags, and any JSON-LD blocks
     * passed in. Called once from the base layout.
     *
     * @param array<int, array<string, mixed>> $jsonLdBlocks
     */
    public static function renderHead(
        string $title,
        ?string $description,
        ?string $canonicalUrl = null,
        ?string $ogImage = null,
        string $ogType = 'website',
        array $jsonLdBlocks = [],
        ?string $robots = null
    ): string {
        $description ??= Settings::get('seo_default_description', '');
        $canonicalUrl ??= self::currentUrl();
        $ogImage ??= self::defaultOgImage();
        $siteName = Settings::get('site_name', 'AlbaTech Solutions');
        $robots ??= 'index, follow';

        $html = '';
        $html .= '<meta name="description" content="' . e(trim($description)) . '">' . "\n";
        $html .= '<meta name="robots" content="' . e($robots) . '">' . "\n";

        if ($keywords = Settings::get('seo_default_keywords')) {
            $html .= '<meta name="keywords" content="' . e($keywords) . '">' . "\n";
        }

        if ($verification = Settings::get('analytics_google_site_verification')) {
            $html .= '<meta name="google-site-verification" content="' . e($verification) . '">' . "\n";
        }
        $html .= '<link rel="canonical" href="' . e($canonicalUrl) . '">' . "\n";

        // Open Graph
        $html .= '<meta property="og:site_name" content="' . e($siteName) . '">' . "\n";
        $html .= '<meta property="og:title" content="' . e($title) . '">' . "\n";
        $html .= '<meta property="og:description" content="' . e($description) . '">' . "\n";
        $html .= '<meta property="og:type" content="' . e($ogType) . '">' . "\n";
        $html .= '<meta property="og:url" content="' . e($canonicalUrl) . '">' . "\n";
        if ($ogImage) {
            $html .= '<meta property="og:image" content="' . e($ogImage) . '">' . "\n";
        }

        // Twitter Card
        $html .= '<meta name="twitter:card" content="' . ($ogImage ? 'summary_large_image' : 'summary') . '">' . "\n";
        $html .= '<meta name="twitter:title" content="' . e($title) . '">' . "\n";
        $html .= '<meta name="twitter:description" content="' . e($description) . '">' . "\n";
        if ($ogImage) {
            $html .= '<meta name="twitter:image" content="' . e($ogImage) . '">' . "\n";
        }

        // JSON-LD structured data — one <script> block per schema type.
        foreach ($jsonLdBlocks as $block) {
            $html .= '<script type="application/ld+json">'
                . json_encode($block, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                . '</script>' . "\n";
        }

        return $html;
    }

    /**
     * Organization schema — who owns this site. Include on every page
     * (site-wide) so search engines and LLMs can confidently attribute
     * content to AlbaTech Solutions.
     */
    public static function organization(): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type'    => 'LocalBusiness',
            'name'     => Settings::get('site_name', 'AlbaTech Solutions'),
            'url'      => Config::get('app.url'),
            'description' => Settings::get('seo_organization_description', 'AlbaTech Solutions helps people and businesses in Kenya get things done digitally, from online assistance and documents to websites and custom software.'),
            'areaServed' => ['@type' => 'Country', 'name' => 'Kenya'],
            'knowsAbout' => ['Digital assistance in Kenya', 'KRA and tax filing assistance', 'eCitizen assistance', 'Business registration assistance', 'SHA and NSSF assistance', 'CV writing', 'Website design', 'Custom software development', 'Google Business Profile setup'],
        ];

        if ($logo = Settings::get('site_logo_path')) {
            $schema['logo'] = url($logo);
        }
        if ($email = Settings::get('contact_email')) {
            $schema['email'] = $email;
        }
        if ($phone = Settings::get('contact_phone')) {
            $schema['telephone'] = $phone;
        }
        if ($address = Settings::get('contact_address')) {
            $schema['address'] = [
                '@type' => 'PostalAddress',
                'streetAddress' => $address,
                'addressCountry' => 'KE',
            ];
        }

        $sameAs = array_filter([
            Settings::get('social_facebook'),
            Settings::get('social_twitter'),
            Settings::get('social_linkedin'),
            Settings::get('social_instagram'),
        ]);
        if ($sameAs) {
            $schema['sameAs'] = array_values($sameAs);
        }

        return $schema;
    }

    /**
     * WebSite schema. Keep this limited to capabilities that actually exist
     * on the public site; do not advertise a search action until a public
     * search endpoint is implemented.
     */
    public static function website(): array
    {
        $url = rtrim(Config::get('app.url'), '/');

        return [
            '@context' => 'https://schema.org',
            '@type'    => 'WebSite',
            'name'     => Settings::get('site_name', 'AlbaTech Solutions'),
            'url'      => $url,
        ];
    }

    public static function landingPage(array $page): array
    {
        $type = match ($page['page_type'] ?? 'general') {
            'industry' => 'CollectionPage',
            'service_intent', 'location' => 'WebPage',
            default => 'WebPage',
        };

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $type,
            'name' => $page['title'],
            'url' => $page['canonical_url'] ?: rtrim(Config::get('app.url'), '/') . '/' . $page['slug'],
            'description' => $page['meta_description'] ?: ($page['excerpt'] ?? ''),
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => Settings::get('site_name', 'AlbaTech Solutions'),
                'url' => rtrim(Config::get('app.url'), '/'),
            ],
        ];

        if (!empty($page['focus_keyword'])) {
            $schema['about'] = $page['focus_keyword'];
        }

        return $schema;
    }

    public static function service(array $service): array
    {
        $schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Service',
            'name'        => $service['name'],
            'description' => $service['summary'] ?? strip_tags((string) ($service['description'] ?? '')),
            'provider'    => [
                '@type' => 'Organization',
                'name'  => Settings::get('site_name', 'AlbaTech Solutions'),
            ],
            'areaServed'  => 'KE',
        ];

        if (($service['price_type'] ?? null) !== 'quote' && !empty($service['price'])) {
            $schema['offers'] = [
                '@type'         => 'Offer',
                'price'         => (string) $service['price'],
                'priceCurrency' => 'KES',
            ];
        }

        return $schema;
    }

    public static function article(array $post): array
    {
        $schema = [
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => $post['title'],
            'description'   => $post['excerpt'] ?? strip_tags((string) ($post['content'] ?? '')),
            'datePublished' => $post['published_at'] ?? $post['created_at'],
            'dateModified'  => $post['updated_at'] ?? $post['published_at'] ?? $post['created_at'],
            'author'        => [
                '@type' => 'Organization',
                'name'  => Settings::get('site_name', 'AlbaTech Solutions'),
            ],
            'publisher'     => [
                '@type' => 'Organization',
                'name'  => Settings::get('site_name', 'AlbaTech Solutions'),
            ],
        ];

        if (!empty($post['featured_media_path'])) {
            $schema['image'] = url($post['featured_media_path']);
        }

        return $schema;
    }

    /**
     * @param array<int, array{question: string, answer: string}> $faqs
     */
    public static function faqPage(array $faqs): array
    {
        return [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => array_map(static fn ($faq) => [
                '@type'          => 'Question',
                'name'           => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => strip_tags($faq['answer']),
                ],
            ], $faqs),
        ];
    }

    /**
     * @param array<int, array{name: string, url: string}> $items Ordered from home to current page.
     */
    public static function breadcrumbs(array $items): array
    {
        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => array_map(
                static fn ($item, $index) => [
                    '@type'    => 'ListItem',
                    'position' => $index + 1,
                    'name'     => $item['name'],
                    'item'     => $item['url'],
                ],
                $items,
                array_keys($items)
            ),
        ];
    }

    private static function currentUrl(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        return rtrim(Config::get('app.url'), '/') . $path;
    }

    private static function defaultOgImage(): ?string
    {
        $logo = Settings::get('site_logo_path');

        return $logo ? url($logo) : null;
    }
}
