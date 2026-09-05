<?php

declare(strict_types=1);

namespace App\Modules\Cms\Controller;

use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Cms\Repository\BlogCategoryRepository;
use App\Modules\Cms\Repository\BlogPostRepository;
use App\Modules\Cms\Repository\FaqRepository;
use App\Modules\Cms\Repository\ServiceCategoryRepository;
use App\Modules\Cms\Repository\ServiceRepository;
use App\Modules\Cms\Service\ContactService;

final class PublicSiteController extends BaseController
{
    public function __construct(
        private readonly BlogPostRepository $posts,
        private readonly BlogCategoryRepository $categories,
        private readonly FaqRepository $faqs,
        private readonly ServiceRepository $services,
        private readonly ServiceCategoryRepository $serviceCategories,
        private readonly ContactService $contactService
    ) {
    }

    public function home(Request $request): Response
    {
        return $this->view('public.home', [
            'faqs' => array_slice($this->faqs->allActive(), 0, 6),
            'recentPosts' => $this->posts->paginatePublished(1, 3),
            'featuredServices' => $this->services->forHomepage(8),
            'homepageServices' => $this->services->allPublished(),
            'serviceCategories' => $this->serviceCategories->withPublishedServices(),
            'serviceCount' => count($this->services->allPublished()),
        ]);
    }

    public function blogIndex(Request $request): Response
    {
        $page = max(1, (int) $request->input('page', 1));
        $categorySlug = $request->input('category');
        return $this->view('public.blog-index', [
            'posts' => $this->posts->paginatePublished($page, 9, $categorySlug ?: null),
            'categories' => $this->categories->allActive(),
            'currentCategory' => $categorySlug,
            'page' => $page,
            'robots' => ($page > 1 || !empty($categorySlug)) ? 'noindex, follow' : null,
        ]);
    }

    public function blogShow(Request $request): Response
    {
        $post = $this->posts->findBySlug((string) $request->param('slug'));
        if (!$post) return $this->view('public.404', [], 404);
        return $this->view('public.blog-show', ['post' => $post]);
    }

    public function faqsPage(Request $request): Response
    {
        return $this->view('public.faqs', ['faqs' => $this->faqs->allActive()]);
    }

    public function servicesIndex(Request $request): Response
    {
        return $this->view('public.services-index', [
            'categories' => $this->serviceCategories->withPublishedServices(),
        ]);
    }

    public function redirectServiceLegacyWebsiteDesign(Request $request): Response { return $this->redirect('/services/website-design-kenya', 301); }
    public function redirectServiceLegacyCr12(Request $request): Response { return $this->redirect('/services/cr12-application', 301); }
    public function redirectServiceLegacyNetworking(Request $request): Response { return $this->redirect('/services/wifi-networking', 301); }

    public function serviceShow(Request $request): Response
    {
        $slug = (string) $request->param('slug');
        $service = $this->services->findBySlug($slug);
        if (!$service) {
            $canonicalSlug = $this->services->canonicalSlugForAlias($slug);
            if ($canonicalSlug !== null) {
                return $this->redirect('/services/' . rawurlencode($canonicalSlug), 301);
            }
            return $this->view('public.404', [], 404);
        }

        $requirements = json_decode((string) ($service['commerce_requirements'] ?? '[]'), true);
        $service['requirements_list'] = is_array($requirements) ? array_values(array_filter(array_map('strval', $requirements))) : [];
        $intakeQuestions = json_decode((string) ($service['intake_questions'] ?? '[]'), true);
        $service['intake_questions_list'] = is_array($intakeQuestions) ? $intakeQuestions : [];
        $faqs = json_decode((string) ($service['faqs'] ?? '[]'), true);
        $service['faq_items'] = is_array($faqs) ? array_values(array_filter(array_map(static function ($faq): array {
            if (!is_array($faq)) return [];
            return ['question' => (string) ($faq['question'] ?? $faq['q'] ?? ''), 'answer' => (string) ($faq['answer'] ?? $faq['a'] ?? '')];
        }, $faqs), static fn (array $faq): bool => $faq['question'] !== '' && $faq['answer'] !== '')) : [];

        $all = $this->services->allPublished();
        $relatedIds = json_decode((string)($service['related_service_ids'] ?? '[]'), true);
        $relatedIds = is_array($relatedIds) ? array_values(array_filter(array_map('intval', $relatedIds))) : [];
        $related = $relatedIds
            ? array_values(array_filter($all, static fn(array $item): bool => in_array((int)$item['id'], $relatedIds, true) && $item['slug'] !== $service['slug']))
            : array_values(array_filter($all, static fn(array $item): bool => $item['slug'] !== $service['slug']));
        $relatedPosts = $this->posts->paginatePublished(1, 3);
        return $this->view('public.service-show', [
            'service' => $service,
            'relatedServices' => array_slice($related, 0, 3),
            'relatedPosts' => $relatedPosts,
        ]);
    }

    public function robotsTxt(Request $request): Response
    {
        $baseUrl = rtrim(Config::get('app.url'), '/');
        $adminPath = Config::get('admin.path', '/admin');
        $loginPath = Config::get('auth.login_path', '/login');
        $lines = [
            'User-agent: *',
            'Allow: /',
            "Disallow: {$adminPath}/",
            "Disallow: {$loginPath}",
            'Disallow: /quote/',
            'Disallow: /request/',
            'Disallow: /review/',
            'Disallow: /receipt/',
            'Disallow: /get-help/thanks',
            '',
            "Sitemap: {$baseUrl}/sitemap.xml",
        ];
        return Response::text(implode("\n", $lines), 200);
    }

    public function sitemapXml(Request $request): Response
    {
        $baseUrl = rtrim(Config::get('app.url'), '/');
        $urls = [
            ['loc' => $baseUrl . '/', 'priority' => '1.0'],
            ['loc' => $baseUrl . '/services', 'priority' => '0.9'],
            ['loc' => $baseUrl . '/get-help', 'priority' => '0.95'],
            ['loc' => $baseUrl . '/blog', 'priority' => '0.7'],
            ['loc' => $baseUrl . '/faqs', 'priority' => '0.5'],
            ['loc' => $baseUrl . '/about', 'priority' => '0.7'],
            ['loc' => $baseUrl . '/contact', 'priority' => '0.6'],
        ];

        foreach ($this->services->allPublished() as $service) {
            $urls[] = ['loc' => $baseUrl . '/services/' . ltrim((string)$service['slug'], '/'), 'lastmod' => $service['updated_at'], 'priority' => '0.8'];
        }
        foreach ($this->posts->allForAdmin() as $post) {
            if (($post['status'] ?? null) === 'published') {
                $urls[] = ['loc' => $baseUrl . '/blog/' . ltrim((string)$post['slug'], '/'), 'lastmod' => $post['updated_at'] ?? $post['created_at'], 'priority' => '0.6'];
            }
        }

        // Deduplicate canonical locations so a high-intent job has exactly one
        // sitemap entry; parameterized/token URLs are never added here.
        $seen = [];
        $urls = array_values(array_filter($urls, static function (array $url) use (&$seen): bool {
            $loc = rtrim((string)$url['loc'], '/') ?: '/';
            if (isset($seen[$loc])) return false;
            $seen[$loc] = true;
            return true;
        }));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1) . "</loc>\n";
            if (!empty($url['lastmod'])) $xml .= '    <lastmod>' . date('Y-m-d', strtotime((string)$url['lastmod'])) . "</lastmod>\n";
            $xml .= '    <priority>' . e((string)$url['priority']) . "</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';
        return Response::html($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function aboutPage(Request $request): Response
    {
        return $this->view('public.about', [
            'serviceCount' => count($this->services->allPublished()),
        ]);
    }

    public function contactPage(Request $request): Response
    {
        return $this->view('public.contact');
    }

    public function contactSubmit(Request $request): Response
    {
        $result = $this->contactService->submit($request->all(), $request->ip());
        if (!$result['success']) {
            Session::flash('_errors', $result['errors']);
            Session::flash('_old', $request->only(['name', 'email', 'phone', 'subject', 'message']));
            return $this->back();
        }
        Session::flash('_success', "Thanks for reaching out — we'll get back to you shortly.");
        return $this->redirect('/contact');
    }
}
