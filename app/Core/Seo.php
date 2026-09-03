<?php

declare(strict_types=1);

namespace App\Core;

final class Seo
{
    public static function pageTitle(string $title): string
    {
        $suffix = ' | AlbaTech Solutions';
        $title = trim($title);
        if ($title === '') {
            return 'AlbaTech Solutions';
        }

        return str_ends_with(mb_strtolower($title), mb_strtolower($suffix)) ? $title : $title . $suffix;
    }

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
        $canonicalUrl = self::normalizeUrl($canonicalUrl ?? self::currentUrl());
        $ogImage = self::resolveImageUrl($ogImage ?? self::defaultOgImage());
        $siteName = Settings::get('site_name', 'AlbaTech Solutions');
        $robots ??= 'index, follow';
        $pageTitle = self::pageTitle($title);

        $html = '<meta name="description" content="' . e(trim($description)) . '">' . "\n";
        $html .= '<meta name="robots" content="' . e($robots) . '">' . "\n";
        if ($keywords = Settings::get('seo_default_keywords')) $html .= '<meta name="keywords" content="' . e($keywords) . '">' . "\n";
        if ($verification = Settings::get('analytics_google_site_verification')) $html .= '<meta name="google-site-verification" content="' . e($verification) . '">' . "\n";
        $html .= '<link rel="canonical" href="' . e($canonicalUrl) . '">' . "\n";
        $html .= '<meta property="og:site_name" content="' . e($siteName) . '">' . "\n";
        $html .= '<meta property="og:title" content="' . e($pageTitle) . '">' . "\n";
        $html .= '<meta property="og:description" content="' . e($description) . '">' . "\n";
        $html .= '<meta property="og:type" content="' . e($ogType) . '">' . "\n";
        $html .= '<meta property="og:url" content="' . e($canonicalUrl) . '">' . "\n";
        if ($ogImage) $html .= '<meta property="og:image" content="' . e($ogImage) . '">' . "\n";
        $html .= '<meta name="twitter:card" content="' . ($ogImage ? 'summary_large_image' : 'summary') . '">' . "\n";
        $html .= '<meta name="twitter:title" content="' . e($pageTitle) . '">' . "\n";
        $html .= '<meta name="twitter:description" content="' . e($description) . '">' . "\n";
        if ($ogImage) $html .= '<meta name="twitter:image" content="' . e($ogImage) . '">' . "\n";

        foreach ($jsonLdBlocks as $block) {
            $html .= '<script type="application/ld+json">' . json_encode($block, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
        }
        return $html;
    }

    public static function organization(): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => Settings::get('site_name', 'AlbaTech Solutions'),
            'url' => rtrim(Config::get('app.url'), '/'),
            'description' => Settings::get('seo_organization_description', 'AlbaTech Solutions helps people and businesses in Kenya get practical digital tasks done.'),
            'areaServed' => ['@type' => 'Country', 'name' => 'Kenya'],
            'knowsAbout' => ['Digital assistance in Kenya', 'KRA assistance', 'eCitizen assistance', 'SHA registration support', 'NSSF assistance', 'NTSA assistance', 'Business registration assistance', 'CV writing', 'Website design', 'Custom software development'],
        ];
        if ($logo = Settings::get('site_logo_path')) $schema['logo'] = self::resolveImageUrl($logo);
        if ($email = Settings::get('contact_email')) $schema['email'] = $email;
        if ($phone = Settings::get('contact_phone')) $schema['telephone'] = $phone;
        if ($address = Settings::get('contact_address')) $schema['address'] = ['@type' => 'PostalAddress', 'streetAddress' => $address, 'addressCountry' => 'KE'];
        $sameAs = array_filter([Settings::get('social_facebook'), Settings::get('social_twitter'), Settings::get('social_linkedin'), Settings::get('social_instagram')]);
        if ($sameAs) $schema['sameAs'] = array_values($sameAs);
        return $schema;
    }

    public static function website(): array
    {
        $url = rtrim(Config::get('app.url'), '/');
        return ['@context' => 'https://schema.org', '@type' => 'WebSite', 'name' => Settings::get('site_name', 'AlbaTech Solutions'), 'url' => $url];
    }

    public static function landingPage(array $page): array
    {
        $type = ($page['page_type'] ?? 'general') === 'industry' ? 'CollectionPage' : 'WebPage';
        $url = self::normalizeUrl($page['canonical_url'] ?: rtrim(Config::get('app.url'), '/') . '/' . ltrim((string)$page['slug'], '/'));
        return ['@context'=>'https://schema.org','@type'=>$type,'name'=>$page['title'],'url'=>$url,'description'=>$page['meta_description'] ?: ($page['excerpt'] ?? ''),'isPartOf'=>['@type'=>'WebSite','name'=>Settings::get('site_name','AlbaTech Solutions'),'url'=>rtrim(Config::get('app.url'),'/')]];
    }

    public static function service(array $service): array
    {
        $schema=['@context'=>'https://schema.org','@type'=>'Service','name'=>$service['name'],'description'=>$service['summary'] ?? strip_tags((string)($service['description'] ?? '')),'provider'=>['@type'=>'Organization','name'=>Settings::get('site_name','AlbaTech Solutions')],'areaServed'=>['@type'=>'Country','name'=>'Kenya']];
        if (($service['price_type'] ?? null) !== 'quote' && !empty($service['price'])) $schema['offers']=['@type'=>'Offer','price'=>(string)$service['price'],'priceCurrency'=>'KES'];
        return $schema;
    }

    public static function article(array $post): array
    {
        $schema=['@context'=>'https://schema.org','@type'=>'Article','headline'=>$post['title'],'description'=>$post['excerpt'] ?? strip_tags((string)($post['content'] ?? '')),'datePublished'=>$post['published_at'] ?? $post['created_at'],'dateModified'=>$post['updated_at'] ?? $post['published_at'] ?? $post['created_at'],'author'=>['@type'=>'Organization','name'=>Settings::get('site_name','AlbaTech Solutions')],'publisher'=>['@type'=>'Organization','name'=>Settings::get('site_name','AlbaTech Solutions')]];
        if (!empty($post['featured_media_path'])) $schema['image']=self::resolveImageUrl($post['featured_media_path']);
        return $schema;
    }

    public static function faqPage(array $faqs): array
    {
        return ['@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>array_map(static fn($faq)=>['@type'=>'Question','name'=>$faq['question'],'acceptedAnswer'=>['@type'=>'Answer','text'=>strip_tags($faq['answer'])]],$faqs)];
    }

    public static function breadcrumbs(array $items): array
    {
        return ['@context'=>'https://schema.org','@type'=>'BreadcrumbList','itemListElement'=>array_map(static fn($item,$index)=>['@type'=>'ListItem','position'=>$index+1,'name'=>$item['name'],'item'=>self::normalizeUrl($item['url'])],$items,array_keys($items))];
    }

    private static function currentUrl(): string { return rtrim(Config::get('app.url'),'/').(parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH)?:'/'); }
    private static function normalizeUrl(string $url): string { $parts=parse_url($url); if($parts===false)return $url; $scheme=isset($parts['scheme'])?$parts['scheme'].'://':'';$host=$parts['host']??'';$port=isset($parts['port'])?':'.$parts['port']:'';$path=$parts['path']??'/'; if($scheme===''||$host==='')return rtrim(Config::get('app.url'),'/').'/'.ltrim($path,'/'); return $scheme.$host.$port.$path; }
    private static function resolveImageUrl(?string $image): ?string { if(!$image)return null; return preg_match('#^https?://#i',$image)?$image:url('/'.ltrim($image,'/')); }
    private static function defaultOgImage(): ?string { $dedicated=Settings::get('seo_default_og_image')?:Settings::get('site_og_image_path'); if($dedicated)return $dedicated; return Settings::get('site_logo_path')?:null; }
}
