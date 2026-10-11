<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\AdminSecurity;
use AlbaTech\Core\Database;
use AlbaTech\Core\Request;
use AlbaTech\Core\Response;
use AlbaTech\Core\View;
use PDO;
use Throwable;

final class CmsAdminController
{
    private const PAGE_SLUGS = ['about', 'get-help', 'contact-details', 'terms', 'privacy'];
    private const HOME_KEYS = [
        'home_hero_eyebrow' => 180, 'home_hero_title' => 220, 'home_hero_lead' => 1200,
        'home_hero_image' => 500, 'home_services_kicker' => 100, 'home_services_title' => 220,
        'home_work_kicker' => 100, 'home_work_title' => 220, 'home_work_description' => 600,
        'home_process_kicker' => 100, 'home_process_title' => 220, 'home_process_description' => 800,
        'home_cta_kicker' => 100, 'home_cta_title' => 220, 'home_cta_description' => 800,
        'home_step1_title' => 180, 'home_step1_description' => 500, 'home_step2_title' => 180, 'home_step2_description' => 500, 'home_step3_title' => 180, 'home_step3_description' => 500,
        'home_why_kicker' => 100, 'home_why_title' => 220, 'home_why_description' => 800, 'home_why1_title' => 180, 'home_why1_description' => 500, 'home_why2_title' => 180, 'home_why2_description' => 500, 'home_why3_title' => 180, 'home_why3_description' => 500,
        'home_areas_kicker' => 100, 'home_areas_title' => 220, 'home_areas_description' => 800, 'home_area1_category' => 100, 'home_area1_title' => 180, 'home_area1_description' => 500, 'home_area2_category' => 100, 'home_area2_title' => 180, 'home_area2_description' => 500, 'home_area3_category' => 100, 'home_area3_title' => 180, 'home_area3_description' => 500, 'home_promise' => 1200, 'home_meta_title' => 255, 'home_meta_description' => 320, 'home_social_image' => 500,
        'home_hero_primary_label' => 100, 'home_hero_primary_url' => 500,
        'home_hero_secondary_label' => 100, 'home_hero_secondary_url' => 500,
    ];

    public function index(): void
    {
        AdminSecurity::requirePermission('settings.manage');
        $pdo = Database::connection();
        $settings = [];
        foreach ($pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_group='homepage'")->fetchAll(PDO::FETCH_ASSOC) as $row) $settings[$row['setting_key']] = $row['setting_value'];
        $pages = $pdo->query('SELECT * FROM cms_pages ORDER BY slug')->fetchAll(PDO::FETCH_ASSOC);
        $navigation = $pdo->query('SELECT * FROM site_navigation ORDER BY placement, sort_order, id')->fetchAll(PDO::FETCH_ASSOC);
        $ctas = $pdo->query('SELECT * FROM site_ctas ORDER BY placement, cta_key')->fetchAll(PDO::FETCH_ASSOC);
        Response::html(View::render('admin/cms', [
            'pageTitle' => 'Content & navigation', 'csrf' => AdminSecurity::csrfToken(),
            'adminUser' => AdminSecurity::user(), 'flash' => AdminSecurity::pullFlash(),
            'settings' => $settings, 'pages' => $pages, 'navigation' => $navigation, 'ctas' => $ctas,
        ], 'layouts/admin'));
    }

    public function saveHomepage(Request $request): void
    {
        $user = AdminSecurity::requirePermission('settings.manage');
        $this->csrfOrRedirect($request, '/admin/cms#homepage');
        $pdo = Database::connection();
        $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key,setting_value,value_type,setting_group,is_public,updated_by) VALUES (:key,:value,:type,'homepage',1,:user) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value), value_type=VALUES(value_type), setting_group='homepage', is_public=1, updated_by=VALUES(updated_by)");
        foreach (self::HOME_KEYS as $key => $max) {
            $value = trim((string) $request->input($key, ''));
            if (mb_strlen($value) > $max) { $this->flashRedirect('error', ucfirst(str_replace('_', ' ', $key)) . ' exceeds ' . $max . ' characters.', '/admin/cms#homepage'); }
            if ((str_ends_with($key, '_url') || $key === 'home_social_image') && $value !== '' && !$this->validInternalOrHttpsUrl($value)) $this->flashRedirect('error', 'Use a local path or a valid HTTPS URL for CTA links.', '/admin/cms#homepage');
            if (in_array($key, ['home_hero_image','home_social_image'], true) && $value !== '' && !$this->validImageReference($value)) $this->flashRedirect('error', 'Hero image must be a local /assets or /uploads image path, or an HTTPS image URL.', '/admin/cms#homepage');
            $stmt->execute(['key' => $key, 'value' => $value, 'type' => str_ends_with($key, '_url') || $key === 'home_hero_image' ? 'url' : 'string', 'user' => $user['id']]);
        }
        // Keep the homepage CTA editor and reusable CTA registry in sync: one source of truth per CTA.
        $ctaSync = $pdo->prepare("INSERT INTO site_ctas (cta_key,label,url,placement,style_class,is_enabled,updated_by) VALUES (:key,:label,:url,:placement,:style,1,:user) ON DUPLICATE KEY UPDATE label=VALUES(label),url=VALUES(url),updated_by=VALUES(updated_by)");
        foreach ([['key'=>'primary_enquiry','label'=>'home_hero_primary_label','url'=>'home_hero_primary_url','placement'=>'home_hero','style'=>'button button--primary'], ['key'=>'explore_services','label'=>'home_hero_secondary_label','url'=>'home_hero_secondary_url','placement'=>'home_hero','style'=>'button button--secondary']] as $cta) {
            $label = trim((string)$request->input($cta['label'], ''));
            $url = trim((string)$request->input($cta['url'], ''));
            if ($label !== '' && $url !== '') $ctaSync->execute(['key'=>$cta['key'],'label'=>$label,'url'=>$url,'placement'=>$cta['placement'],'style'=>$cta['style'],'user'=>$user['id']]);
        }
        $this->audit('cms.homepage.updated', 'homepage', null, array_keys(self::HOME_KEYS));
        $this->flashRedirect('success', 'Homepage content saved.', '/admin/cms#homepage');
    }

    public function savePage(Request $request, array $params = []): void
    {
        $user = AdminSecurity::requirePermission('settings.manage');
        $this->csrfOrRedirect($request, '/admin/cms#pages');
        $slug = strtolower(trim((string) $request->input('slug', '')));
        if (!in_array($slug, self::PAGE_SLUGS, true)) $this->flashRedirect('error', 'This page slug is not editable here.', '/admin/cms#pages');
        $title = trim((string) $request->input('title', ''));
        $intro = trim((string) $request->input('intro', ''));
        $body = trim((string) $request->input('body_content', ''));
        $metaTitle = trim((string) $request->input('meta_title', ''));
        $description = trim((string) $request->input('meta_description', ''));
        $image = trim((string) $request->input('social_image', ''));
        $status = (string) $request->input('status', 'published');
        if ($title === '' || mb_strlen($title) > 200 || mb_strlen($intro) > 5000 || mb_strlen($body) > 50000 || mb_strlen($metaTitle) > 255 || mb_strlen($description) > 320 || !in_array($status, ['published', 'draft'], true)) $this->flashRedirect('error', 'Check required fields, lengths and publication status.', '/admin/cms#pages');
        if ($image !== '' && !$this->validImageReference($image)) $this->flashRedirect('error', 'Social image must be a local /assets or /uploads image path, or an HTTPS image URL.', '/admin/cms#pages');
        Database::connection()->prepare('UPDATE cms_pages SET title=:title,intro=:intro,body_content=:body,meta_title=:meta_title,meta_description=:description,social_image=:image,status=:status,updated_by=:user WHERE slug=:slug')->execute([
            'title' => $title, 'intro' => $intro, 'body' => $body, 'meta_title' => $metaTitle,
            'description' => $description, 'image' => $image, 'status' => $status, 'user' => $user['id'], 'slug' => $slug,
        ]);
        $this->audit('cms.page.updated', 'cms_page', $slug, ['status' => $status]);
        $this->flashRedirect('success', 'Page saved.', '/admin/cms#pages');
    }

    public function saveNavigation(Request $request, array $params = []): void
    {
        $user = AdminSecurity::requirePermission('settings.manage');
        $this->csrfOrRedirect($request, '/admin/cms#navigation');
        $id = (int) ($params['id'] ?? 0);
        $label = trim((string) $request->input('label', ''));
        $url = trim((string) $request->input('url', ''));
        $placement = (string) $request->input('placement', 'header');
        if ($label === '' || mb_strlen($label) > 100 || !$this->validInternalOrHttpsUrl($url) || !in_array($placement, ['header', 'footer_explore', 'footer_information'], true)) $this->flashRedirect('error', 'Enter a label, a safe local/HTTPS URL and a valid navigation placement.', '/admin/cms#navigation');
        $data = ['label' => $label, 'url' => $url, 'placement' => $placement, 'sort_order' => max(0, min(10000, (int) $request->input('sort_order', 0))), 'enabled' => $request->input('is_enabled') === '1' ? 1 : 0, 'new_tab' => $request->input('open_new_tab') === '1' ? 1 : 0, 'user' => $user['id']];
        if ($id > 0) { $data['id'] = $id; Database::connection()->prepare('UPDATE site_navigation SET label=:label,url=:url,placement=:placement,sort_order=:sort_order,is_enabled=:enabled,open_new_tab=:new_tab,updated_by=:user WHERE id=:id')->execute($data); }
        else Database::connection()->prepare('INSERT INTO site_navigation (label,url,placement,sort_order,is_enabled,open_new_tab,updated_by) VALUES (:label,:url,:placement,:sort_order,:enabled,:new_tab,:user)')->execute($data);
        $this->audit('cms.navigation.saved', 'navigation', $id ? (string) $id : (string) Database::connection()->lastInsertId(), ['label' => $label]);
        $this->flashRedirect('success', 'Navigation item saved.', '/admin/cms#navigation');
    }

    public function deleteNavigation(Request $request, array $params): void
    {
        AdminSecurity::requirePermission('settings.manage'); $this->csrfOrRedirect($request, '/admin/cms#navigation');
        Database::connection()->prepare('DELETE FROM site_navigation WHERE id=:id')->execute(['id' => (int) ($params['id'] ?? 0)]);
        $this->flashRedirect('success', 'Navigation item removed.', '/admin/cms#navigation');
    }

    public function saveCta(Request $request, array $params = []): void
    {
        $user = AdminSecurity::requirePermission('settings.manage'); $this->csrfOrRedirect($request, '/admin/cms#ctas');
        $id = (int) ($params['id'] ?? 0); $key = strtolower(trim((string) $request->input('cta_key', '')));
        $label = trim((string) $request->input('label', '')); $url = trim((string) $request->input('url', ''));
        $placement = trim((string) $request->input('placement', 'global')); $style = trim((string) $request->input('style_class', 'button button--primary'));
        if (!preg_match('/^[a-z][a-z0-9_]{2,99}$/', $key) || $label === '' || mb_strlen($label) > 150 || !$this->validInternalOrHttpsUrl($url) || !preg_match('/^[a-zA-Z0-9 _-]{1,80}$/', $placement) || !preg_match('/^[a-zA-Z0-9 _-]{1,80}$/', $style)) $this->flashRedirect('error', 'Check CTA key, label, URL, placement and style class.', '/admin/cms#ctas');
        $data = ['key' => $key, 'label' => $label, 'url' => $url, 'placement' => $placement, 'style' => $style, 'enabled' => $request->input('is_enabled') === '1' ? 1 : 0, 'user' => $user['id']];
        try {
            if ($id > 0) { $data['id'] = $id; Database::connection()->prepare('UPDATE site_ctas SET cta_key=:key,label=:label,url=:url,placement=:placement,style_class=:style,is_enabled=:enabled,updated_by=:user WHERE id=:id')->execute($data); }
            else Database::connection()->prepare('INSERT INTO site_ctas (cta_key,label,url,placement,style_class,is_enabled,updated_by) VALUES (:key,:label,:url,:placement,:style,:enabled,:user)')->execute($data);
        } catch (Throwable $e) { $this->flashRedirect('error', 'Could not save CTA. The CTA key may already be in use.', '/admin/cms#ctas'); }
        $this->audit('cms.cta.saved', 'cta', $id ? (string) $id : (string) Database::connection()->lastInsertId(), ['key' => $key]);
        $this->flashRedirect('success', 'Call to action saved.', '/admin/cms#ctas');
    }

    public function deleteCta(Request $request, array $params): void
    {
        AdminSecurity::requirePermission('settings.manage'); $this->csrfOrRedirect($request, '/admin/cms#ctas');
        Database::connection()->prepare('DELETE FROM site_ctas WHERE id=:id')->execute(['id' => (int) ($params['id'] ?? 0)]);
        $this->flashRedirect('success', 'Call to action removed.', '/admin/cms#ctas');
    }

    private function csrfOrRedirect(Request $request, string $target): void
    { if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) $this->flashRedirect('error', 'Your session expired. Please try again.', $target); }
    private function validInternalOrHttpsUrl(string $url): bool
    { return ($url !== '' && str_starts_with($url, '/') && !str_starts_with($url, '//') && !preg_match('/[\x00-\x20\\]/', $url)) || (filter_var($url, FILTER_VALIDATE_URL) !== false && strtolower((string) parse_url($url, PHP_URL_SCHEME)) === 'https'); }
    private function validImageReference(string $image): bool
    { return $this->validInternalOrHttpsUrl($image) && (str_starts_with($image, '/assets/') || str_starts_with($image, '/uploads/') || str_starts_with($image, 'https://')); }
    private function flashRedirect(string $type, string $message, string $target): never
    { AdminSecurity::flash($type, $message); Response::redirect($target); }
    private function audit(string $action, string $type, ?string $id, array $details): void
    { try { $user = AdminSecurity::user(); Database::connection()->prepare('INSERT INTO admin_audit_logs (admin_user_id,action,entity_type,entity_id,details,ip_address) VALUES (:uid,:action,:type,:eid,:details,:ip)')->execute(['uid'=>$user['id']??null,'action'=>$action,'type'=>$type,'eid'=>$id,'details'=>json_encode($details, JSON_UNESCAPED_SLASHES),'ip'=>substr((string)($_SERVER['REMOTE_ADDR']??''),0,45)]); } catch (Throwable $e) { error_log('[AlbaTech CMS audit] '.$e->getMessage()); } }
}
