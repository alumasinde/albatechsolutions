<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\AdminSecurity;
use AlbaTech\Core\Database;
use AlbaTech\Core\Env;
use AlbaTech\Core\Request;
use AlbaTech\Core\Response;
use AlbaTech\Core\RateLimiter;
use AlbaTech\Core\View;
use PDO;
use Throwable;

final class AdminController
{
    public function login(Request $request): void
    {
        AdminSecurity::startSession();
        if (AdminSecurity::user() !== null) Response::redirect('/admin');
        $error = null;
        if ($request->method() === 'POST') {
            if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) {
                $error = 'Your session expired. Refresh the page and try again.';
            } else {
                $email = strtolower(trim((string) $request->input('email', '')));
                $password = (string) $request->input('password', '');
                $ipKey = RateLimiter::key('admin-login-ip', '*');
                $identityKey = RateLimiter::key('admin-login-identity', substr($email, 0, 190));
                if (RateLimiter::tooManyAttempts($ipKey, 20, 900) || RateLimiter::tooManyAttempts($identityKey, 6, 900)) {
                    http_response_code(429);
                    header('Retry-After: 900');
                    $error = 'Too many sign-in attempts. Please wait 15 minutes before trying again.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190 || $password === '' || strlen($password) > 4096) {
                    RateLimiter::hit($ipKey, 900);
                    RateLimiter::hit($identityKey, 900);
                    $error = 'Email or password is incorrect.';
                } else {
                    $stmt = Database::connection()->prepare("SELECT id, first_name, last_name, email, password_hash FROM admin_users WHERE email = :email AND status = 'active' LIMIT 1");
                    $stmt->execute(['email' => $email]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($user && password_verify($password, (string) $user['password_hash'])) {
                        RateLimiter::clear($identityKey);
                        session_regenerate_id(true);
                        $_SESSION['_created_at'] = time();
                        $_SESSION['_last_activity'] = time();
                        AdminSecurity::rotateCsrfToken();
                        $_SESSION['admin_user'] = ['id' => (int) $user['id'], 'first_name' => $user['first_name'], 'last_name' => $user['last_name'], 'email' => $user['email']];
                        Database::connection()->prepare('UPDATE admin_users SET last_login_at = NOW() WHERE id = :id')->execute(['id' => $user['id']]);
                        $this->audit('login', 'admin_user', (string) $user['id'], []);
                        Response::redirect('/admin');
                    }
                    if ($error === null) {
                        RateLimiter::hit($ipKey, 900);
                        RateLimiter::hit($identityKey, 900);
                        $error = 'Email or password is incorrect.';
                    }
                }
            }
        }
        Response::html(View::render('admin/login', ['pageTitle' => 'Admin sign in', 'csrf' => AdminSecurity::csrfToken(), 'error' => $error], 'layouts/admin-guest'));
    }

    public function logout(Request $request): void
    {
        AdminSecurity::requireUser();
        if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) {
            AdminSecurity::flash('error', 'Your session expired. Please try again.');
            Response::redirect('/admin');
        }
        AdminSecurity::destroySession();
        Response::redirect('/admin/login');
    }

    public function dashboard(): void
    {
        AdminSecurity::requireUser();
        $pdo = Database::connection();
        $stats = [
            'services' => (int) $pdo->query('SELECT COUNT(*) FROM services')->fetchColumn(),
            'published' => (int) $pdo->query("SELECT COUNT(*) FROM services WHERE status = 'published'")->fetchColumn(),
            'featured' => (int) $pdo->query("SELECT COUNT(*) FROM services WHERE status = 'published' AND is_featured = 1")->fetchColumn(),
            'categories' => (int) $pdo->query("SELECT COUNT(*) FROM service_categories WHERE status = 'active'")->fetchColumn(),
        ];
        $recent = $pdo->query('SELECT id, name, status, pricing_type, pricing_amount, is_featured, updated_at FROM services ORDER BY updated_at DESC LIMIT 6')->fetchAll();
        $this->render('dashboard', ['pageTitle' => 'Overview', 'stats' => $stats, 'recent' => $recent]);
    }

    public function services(): void
    {
        AdminSecurity::requirePermission('services.manage');
        $stmt = Database::connection()->query('SELECT s.*, c.name AS category_name FROM services s INNER JOIN service_categories c ON c.id = s.category_id ORDER BY s.sort_order, s.name');
        $categories = Database::connection()->query('SELECT id, name, status FROM service_categories ORDER BY sort_order, name')->fetchAll();
        $this->render('services', ['pageTitle' => 'Services', 'services' => $stmt->fetchAll(), 'categories' => $categories]);
    }

    public function serviceCreate(): void
    {
        AdminSecurity::requirePermission('services.manage');
        $categories = Database::connection()->query('SELECT id, name FROM service_categories WHERE status = \'active\' ORDER BY sort_order, name')->fetchAll();
        $this->render('service-form', ['pageTitle' => 'Add service', 'service' => $this->emptyService(), 'categories' => $categories, 'formAction' => '/admin/services/create']);
    }

    public function serviceEdit(Request $request, array $params): void
    {
        AdminSecurity::requirePermission('services.manage');
        $stmt = Database::connection()->prepare('SELECT * FROM services WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => (int) ($params['id'] ?? 0)]);
        $service = $stmt->fetch();
        if (!$service) {
            AdminSecurity::flash('error', 'Service not found.');
            Response::redirect('/admin/services');
        }
        $categories = Database::connection()->query('SELECT id, name FROM service_categories ORDER BY sort_order, name')->fetchAll();
        $this->render('service-form', ['pageTitle' => 'Edit service', 'service' => $service, 'categories' => $categories, 'formAction' => '/admin/services/' . (int) $service['id'] . '/edit']);
    }

    public function serviceSave(Request $request, array $params = []): void
    {
        $user = AdminSecurity::requirePermission('services.manage');
        if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) {
            AdminSecurity::flash('error', 'Your session expired. Please submit the form again.');
            Response::redirect('/admin/services');
        }
        $id = (int) ($params['id'] ?? 0);
        $name = trim((string) $request->input('name', ''));
        $slug = trim((string) $request->input('slug', ''));
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $slug !== '' ? $slug : $name) ?? '', '-'));
        $categoryId = (int) $request->input('category_id', 0);
        $pricingType = (string) $request->input('pricing_type', 'quote');
        $status = (string) $request->input('status', 'draft');
        $amountInput = trim((string) $request->input('pricing_amount', ''));
        $amount = $amountInput !== '' && is_numeric($amountInput) && (float) $amountInput >= 0 ? number_format((float) $amountInput, 2, '.', '') : null;
        $errors = [];
        if ($name === '' || mb_strlen($name) > 180) $errors[] = 'Enter a service name of up to 180 characters.';
        foreach (['short_description' => 500, 'description' => 12000, 'who_for' => 5000, 'what_included' => 5000, 'what_needed' => 5000, 'meta_title' => 255, 'meta_description' => 320, 'turnaround' => 80] as $field => $limit) {
            if (mb_strlen(trim((string) $request->input($field, ''))) > $limit) $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is too long (maximum ' . $limit . ' characters).';
        }
        if ($slug === '' || mb_strlen($slug) > 200) $errors[] = 'Enter a valid URL slug.';
        if ($categoryId < 1) $errors[] = 'Choose a category.';
        else {
            $categoryCheck = Database::connection()->prepare("SELECT COUNT(*) FROM service_categories WHERE id = :id AND status = 'active'");
            $categoryCheck->execute(['id' => $categoryId]);
            if (!(int) $categoryCheck->fetchColumn()) $errors[] = 'Choose an active category.';
        }
        if ($amountInput !== '' && ($amount === null || !preg_match('/^\d{1,10}(?:\.\d{1,2})?$/', $amountInput) || (float) $amount > 999999999.99)) $errors[] = 'Enter a valid price below KSh 1,000,000,000.';
        if (!in_array($pricingType, ['fixed', 'starting_at', 'quote'], true)) $errors[] = 'Choose a valid pricing option.';
        if (!in_array($status, ['published', 'draft', 'archived'], true)) $errors[] = 'Choose a valid publication status.';
        if ($pricingType !== 'quote' && $amount === null) $errors[] = 'Enter a valid price, or choose Contact for price.';
        if ($pricingType === 'quote') $amount = null;
        if ($errors) {
            $categories = Database::connection()->query('SELECT id, name FROM service_categories ORDER BY sort_order, name')->fetchAll();
            $service = array_merge($this->emptyService(), $_POST, ['id' => $id, 'slug' => $slug]);
            $this->render('service-form', ['pageTitle' => $id ? 'Edit service' : 'Add service', 'service' => $service, 'categories' => $categories, 'formAction' => $id ? '/admin/services/' . $id . '/edit' : '/admin/services/create', 'errors' => $errors]);
            return;
        }
        $data = [
            'category_id' => $categoryId,
            'name' => $name,
            'slug' => $slug,
            'short_description' => trim((string) $request->input('short_description', '')),
            'description' => trim((string) $request->input('description', '')),
            'who_for' => trim((string) $request->input('who_for', '')),
            'what_included' => trim((string) $request->input('what_included', '')),
            'what_needed' => trim((string) $request->input('what_needed', '')),
            'pricing_type' => $pricingType,
            'pricing_amount' => $amount,
            'pricing_label' => $pricingType === 'quote' ? 'Contact for price' : null,
            'turnaround' => trim((string) $request->input('turnaround', '')) ?: null,
            'meta_title' => trim((string) $request->input('meta_title', '')) ?: null,
            'meta_description' => trim((string) $request->input('meta_description', '')) ?: null,
            'status' => $status,
            'is_featured' => $request->input('is_featured') === '1' ? 1 : 0,
            'sort_order' => max(0, (int) $request->input('sort_order', 0)),
        ];
        $pdo = Database::connection();
        try {
            if ($id > 0) {
                $exists = $pdo->prepare('SELECT id FROM services WHERE id = :id LIMIT 1');
                $exists->execute(['id' => $id]);
                if (!$exists->fetchColumn()) {
                    AdminSecurity::flash('error', 'Service not found.');
                    Response::redirect('/admin/services');
                }
                $data['id'] = $id;
                $sql = 'UPDATE services SET category_id=:category_id, name=:name, slug=:slug, short_description=:short_description, description=:description, who_for=:who_for, what_included=:what_included, what_needed=:what_needed, pricing_type=:pricing_type, pricing_amount=:pricing_amount, pricing_label=:pricing_label, turnaround=:turnaround, meta_title=:meta_title, meta_description=:meta_description, status=:status, is_featured=:is_featured, sort_order=:sort_order WHERE id=:id';
                $pdo->prepare($sql)->execute($data);
                $this->audit('service.updated', 'service', (string) $id, ['name' => $name]);
                AdminSecurity::flash('success', 'Service updated successfully.');
            } else {
                $sql = 'INSERT INTO services (category_id,name,slug,short_description,description,who_for,what_included,what_needed,pricing_type,pricing_amount,pricing_label,turnaround,meta_title,meta_description,status,is_featured,sort_order) VALUES (:category_id,:name,:slug,:short_description,:description,:who_for,:what_included,:what_needed,:pricing_type,:pricing_amount,:pricing_label,:turnaround,:meta_title,:meta_description,:status,:is_featured,:sort_order)';
                $pdo->prepare($sql)->execute($data);
                $newId = (int) $pdo->lastInsertId();
                $this->audit('service.created', 'service', (string) $newId, ['name' => $name]);
                AdminSecurity::flash('success', 'Service created successfully.');
            }
        } catch (Throwable $e) {
            error_log('[AlbaTech Admin] ' . $e->getMessage());
            AdminSecurity::flash('error', str_contains(strtolower($e->getMessage()), 'duplicate') ? 'That URL slug is already in use. Choose another one.' : 'Could not save the service. Check the required fields and try again.');
            Response::redirect($id ? '/admin/services/' . $id . '/edit' : '/admin/services/create');
        }
        Response::redirect('/admin/services');
    }

    public function serviceToggle(Request $request, array $params): void
    {
        $user = AdminSecurity::requirePermission('services.manage');
        if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) {
            AdminSecurity::flash('error', 'Your session expired. Try again.');
            Response::redirect('/admin/services');
        }
        $id = (int) ($params['id'] ?? 0);
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT name, status FROM services WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $service = $stmt->fetch();
        if (!$service) {
            AdminSecurity::flash('error', 'Service not found.');
            Response::redirect('/admin/services');
        }
        $next = $service['status'] === 'published' ? 'draft' : 'published';
        $pdo->prepare('UPDATE services SET status = :status WHERE id = :id')->execute(['status' => $next, 'id' => $id]);
        $this->audit('service.visibility_changed', 'service', (string) $id, ['status' => $next]);
        AdminSecurity::flash('success', $next === 'published' ? 'Service is now live.' : 'Service is now hidden from the public website.');
        Response::redirect('/admin/services');
    }

    public function serviceFeature(Request $request, array $params): void
    {
        AdminSecurity::requirePermission('services.manage');
        if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) {
            AdminSecurity::flash('error', 'Your session expired. Try again.');
            Response::redirect('/admin/services');
        }
        $id = (int) ($params['id'] ?? 0);
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT name, is_featured FROM services WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $service = $stmt->fetch();
        if (!$service) {
            AdminSecurity::flash('error', 'Service not found.');
            Response::redirect('/admin/services');
        }
        $next = (int) $service['is_featured'] === 1 ? 0 : 1;
        $pdo->prepare('UPDATE services SET is_featured = :featured WHERE id = :id')->execute(['featured' => $next, 'id' => $id]);
        $this->audit('service.featured_changed', 'service', (string) $id, ['is_featured' => $next]);
        AdminSecurity::flash('success', $next ? 'Service will be featured on the homepage when published.' : 'Service removed from homepage features.');
        Response::redirect('/admin/services');
    }

    public function categories(): void
    {
        AdminSecurity::requirePermission('categories.manage');
        $categories = Database::connection()->query('SELECT c.*, (SELECT COUNT(*) FROM services s WHERE s.category_id = c.id) AS service_count FROM service_categories c ORDER BY c.sort_order, c.name')->fetchAll();
        $this->render('categories', ['pageTitle' => 'Categories', 'categories' => $categories]);
    }

    public function categorySave(Request $request, array $params = []): void
    {
        AdminSecurity::requirePermission('categories.manage');
        if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) {
            AdminSecurity::flash('error', 'Your session expired. Try again.');
            Response::redirect('/admin/categories');
        }
        $id = (int) ($params['id'] ?? 0);
        $name = trim((string) $request->input('name', ''));
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', (string) ($request->input('slug') ?: $name)) ?? '', '-'));
        $description = trim((string) $request->input('description', ''));
        $status = $request->input('status') === 'inactive' ? 'inactive' : 'active';
        if ($name === '' || mb_strlen($name) > 160 || $slug === '' || mb_strlen($slug) > 180 || mb_strlen($description) > 2000 || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            AdminSecurity::flash('error', 'Enter a valid category name, URL slug and description.');
            Response::redirect('/admin/categories');
        }
        try {
            $pdo = Database::connection();
            if ($id) {
                $exists = $pdo->prepare('SELECT id FROM service_categories WHERE id = :id LIMIT 1');
                $exists->execute(['id' => $id]);
                if (!$exists->fetchColumn()) {
                    AdminSecurity::flash('error', 'Category not found.');
                    Response::redirect('/admin/categories');
                }
                $pdo->prepare('UPDATE service_categories SET name=:name, slug=:slug, description=:description, status=:status, sort_order=:sort_order WHERE id=:id')->execute(['name' => $name, 'slug' => $slug, 'description' => $description, 'status' => $status, 'sort_order' => max(0, (int)$request->input('sort_order', 0)), 'id' => $id]);
                $this->audit('category.updated', 'category', (string) $id, ['name' => $name, 'status' => $status]);
            } else {
                $pdo->prepare('INSERT INTO service_categories (name,slug,description,status,sort_order) VALUES (:name,:slug,:description,:status,:sort_order)')->execute(['name' => $name, 'slug' => $slug, 'description' => $description, 'status' => $status, 'sort_order' => max(0, (int)$request->input('sort_order', 0))]);
                $this->audit('category.created', 'category', (string) $pdo->lastInsertId(), ['name' => $name, 'status' => $status]);
            }
            AdminSecurity::flash('success', 'Category saved.');
        } catch (Throwable $e) {
            error_log('[AlbaTech category] ' . $e->getMessage());
            AdminSecurity::flash('error', 'Could not save category. Check that its slug is unique and try again.');
        }
        Response::redirect('/admin/categories');
    }

    public function settings(): void
    {
        AdminSecurity::requirePermission('settings.manage');
        $rows = Database::connection()->query('SELECT setting_key, setting_value, setting_group FROM site_settings ORDER BY setting_group, setting_key')->fetchAll();
        $settings = [];
        foreach ($rows as $row) $settings[$row['setting_key']] = $row;
        $whatsAppEnv = [];
        foreach (['WHATSAPP_PHONE_NUMBER_ID', 'WHATSAPP_BUSINESS_ACCOUNT_ID', 'WHATSAPP_ACCESS_TOKEN', 'WHATSAPP_VERIFY_TOKEN', 'WHATSAPP_APP_SECRET'] as $envKey) {
            $whatsAppEnv[$envKey] = trim((string) Env::get($envKey, '')) !== '';
        }
        $logFile = dirname(__DIR__, 2) . '/storage/logs/whatsapp-webhook.log';
        $logTail = is_file($logFile) ? array_slice(array_values(array_filter(array_map('trim', file($logFile) ?: []))), -8) : [];
        $this->render('settings', ['pageTitle' => 'Site settings', 'settings' => $settings, 'whatsAppEnv' => $whatsAppEnv, 'whatsAppLogTail' => $logTail]);
    }

    public function settingsSave(Request $request): void
    {
        $user = AdminSecurity::requirePermission('settings.manage');
        if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) {
            AdminSecurity::flash('error', 'Your session expired. Try again.');
            Response::redirect('/admin/settings');
        }
        $contactKeys = ['whatsapp_number', 'contact_email', 'business_phone', 'business_address', 'business_city', 'service_area', 'contact_hours', 'contact_response_time', 'support_email', 'whatsapp_prefill_message', 'business_latitude', 'business_longitude'];
        $socialKeys = ['social_facebook', 'social_twitter', 'social_linkedin', 'social_instagram'];
        $analyticsKeys = ['analytics_enabled', 'analytics_provider', 'analytics_domain', 'analytics_script_url', 'analytics_consent_enabled'];
        $founderKeys = ['founder_name', 'founder_title', 'founder_bio', 'founder_linkedin'];
        $whatsAppApiKeys = ['whatsapp_api_enabled', 'whatsapp_business_display_name', 'whatsapp_notify_number', 'whatsapp_template_name', 'whatsapp_template_language', 'whatsapp_ack_enabled', 'whatsapp_auto_reply_enabled', 'whatsapp_auto_reply_text', 'whatsapp_keyword_replies', 'whatsapp_email_alerts'];
        $allowed = array_merge(['site_name', 'site_url', 'default_author', 'default_meta_title', 'default_meta_description', 'default_social_image', 'google_site_verification'], $founderKeys, $contactKeys, $socialKeys, $analyticsKeys, $whatsAppApiKeys, ['footer_description']);
        $normalized = [];
        $siteUrl = trim((string) $request->input('site_url', ''));
        $defaultSocialImage = trim((string) $request->input('default_social_image', ''));
        $contactEmail = trim((string) $request->input('contact_email', ''));
        $supportEmail = trim((string) $request->input('support_email', ''));
        if ($supportEmail !== '' && !filter_var($supportEmail, FILTER_VALIDATE_EMAIL)) { AdminSecurity::flash('error', 'Enter a valid support email address.'); Response::redirect('/admin/settings'); }
        $whatsAppInput = trim((string) $request->input('whatsapp_number', ''));
        // Store WhatsApp destinations in canonical international digits-only format.
        $whatsApp = preg_replace('/\D+/', '', $whatsAppInput) ?? '';
        if ($siteUrl !== '' && (!filter_var($siteUrl, FILTER_VALIDATE_URL) || !in_array(strtolower((string) parse_url($siteUrl, PHP_URL_SCHEME)), ['http', 'https'], true))) {
            AdminSecurity::flash('error', 'Enter a valid website URL beginning with http:// or https://.');
            Response::redirect('/admin/settings');
        }
        if ($defaultSocialImage !== '' && (str_starts_with($defaultSocialImage, '//') || (preg_match('~^https://[^\s]+$~i', $defaultSocialImage) !== 1 && !preg_match('~^/(?!/)\S*$~', $defaultSocialImage)))) {
            AdminSecurity::flash('error', 'Default social image must be an HTTPS URL or a site-relative path such as /assets/images/albatech-social-card.jpg.');
            Response::redirect('/admin/settings');
        }
        if ($contactEmail !== '' && !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
            AdminSecurity::flash('error', 'Enter a valid contact email address.');
            Response::redirect('/admin/settings');
        }
        if ($whatsAppInput !== '' && (!preg_match('/^[+0-9() .-]{7,25}$/', $whatsAppInput) || strlen($whatsApp) < 8 || strlen($whatsApp) > 15 || str_starts_with($whatsApp, '0'))) {
            AdminSecurity::flash('error', 'Enter a valid WhatsApp contact number.');
            Response::redirect('/admin/settings');
        }
        $notifyInput = trim((string) $request->input('whatsapp_notify_number', ''));
        $notifyDigits = preg_replace('/\D+/', '', $notifyInput) ?? '';
        if ($notifyInput !== '' && (!preg_match('/^[+0-9() .-]{7,25}$/', $notifyInput) || strlen($notifyDigits) < 8 || strlen($notifyDigits) > 15 || str_starts_with($notifyDigits, '0'))) {
            AdminSecurity::flash('error', 'Enter a valid WhatsApp notification number in international format.');
            Response::redirect('/admin/settings');
        }
        $normalized['whatsapp_notify_number'] = $notifyDigits;
        $normalized['whatsapp_api_enabled'] = ((string) $request->input('whatsapp_api_enabled', '0')) === '1' ? '1' : '0';
        $templateName = trim((string) $request->input('whatsapp_template_name', ''));
        if ($templateName !== '' && preg_match('/^[a-z0-9_]{1,512}$/', $templateName) !== 1) {
            AdminSecurity::flash('error', 'Template names use lowercase letters, numbers and underscores only.');
            Response::redirect('/admin/settings');
        }
        $normalized['whatsapp_template_name'] = $templateName;
        $templateLanguage = trim((string) $request->input('whatsapp_template_language', ''));
        if ($templateLanguage !== '' && preg_match('/^[a-z]{2}(_[A-Z]{2})?$/', $templateLanguage) !== 1) {
            AdminSecurity::flash('error', 'Template language should be a code like en or en_US.');
            Response::redirect('/admin/settings');
        }
        $normalized['whatsapp_template_language'] = $templateLanguage;
        foreach (['whatsapp_ack_enabled', 'whatsapp_auto_reply_enabled', 'whatsapp_keyword_replies', 'whatsapp_email_alerts'] as $waToggle) {
            $normalized[$waToggle] = ((string) $request->input($waToggle, '0')) === '1' ? '1' : '0';
        }
        $normalized['whatsapp_auto_reply_text'] = mb_substr(trim((string) $request->input('whatsapp_auto_reply_text', '')), 0, 1000);
        foreach ($socialKeys as $key) {
            $socialUrl = trim((string) $request->input($key, ''));
            if ($socialUrl !== '' && (!filter_var($socialUrl, FILTER_VALIDATE_URL) || preg_match('~^https://~i', $socialUrl) !== 1)) {
                AdminSecurity::flash('error', 'Social profile links must be full URLs starting with https://.');
                Response::redirect('/admin/settings');
            }
        }
        $latitude = trim((string) $request->input('business_latitude', ''));
        $longitude = trim((string) $request->input('business_longitude', ''));
        if (($latitude === '') !== ($longitude === '')) {
            AdminSecurity::flash('error', 'Enter both business latitude and longitude, or leave both blank.');
            Response::redirect('/admin/settings');
        }
        if ($latitude !== '' && (!is_numeric($latitude) || (float) $latitude < -90 || (float) $latitude > 90)) {
            AdminSecurity::flash('error', 'Business latitude must be a number between -90 and 90.');
            Response::redirect('/admin/settings');
        }
        if ($longitude !== '' && (!is_numeric($longitude) || (float) $longitude < -180 || (float) $longitude > 180)) {
            AdminSecurity::flash('error', 'Business longitude must be a number between -180 and 180.');
            Response::redirect('/admin/settings');
        }
        $founderLinkedin = trim((string) $request->input('founder_linkedin', ''));
        if ($founderLinkedin !== '' && (!filter_var($founderLinkedin, FILTER_VALIDATE_URL) || preg_match('~^https://~i', $founderLinkedin) !== 1)) {
            AdminSecurity::flash('error', 'The founder LinkedIn link must be a full URL starting with https://.');
            Response::redirect('/admin/settings');
        }
        $googleVerification = trim((string) $request->input('google_site_verification', ''));
        if ($googleVerification !== '' && preg_match('/^[A-Za-z0-9_-]{8,120}$/', $googleVerification) !== 1) {
            AdminSecurity::flash('error', 'The Search Console verification token should contain only letters, numbers, hyphens and underscores.');
            Response::redirect('/admin/settings');
        }
        $normalized['google_site_verification'] = $googleVerification;
        $analyticsEnabled = (string) $request->input('analytics_enabled', '0');
        if (!in_array($analyticsEnabled, ['0', '1'], true)) $analyticsEnabled = '0';
        $normalized['analytics_enabled'] = $analyticsEnabled;
        $analyticsConsent = (string) $request->input('analytics_consent_enabled', '1');
        if (!in_array($analyticsConsent, ['0', '1'], true)) $analyticsConsent = '1';
        $normalized['analytics_consent_enabled'] = $analyticsConsent;
        $analyticsProvider = trim((string) $request->input('analytics_provider', ''));
        if (!in_array($analyticsProvider, ['', 'plausible', 'ga4'], true)) $analyticsProvider = '';
        $normalized['analytics_provider'] = $analyticsProvider;
        $analyticsScriptUrl = trim((string) $request->input('analytics_script_url', ''));
        if ($analyticsEnabled === '1' && $analyticsProvider === '') {
            AdminSecurity::flash('error', 'Choose an analytics provider before enabling analytics.');
            Response::redirect('/admin/settings');
        }
        if ($analyticsEnabled === '1' && ($analyticsScriptUrl === '' || preg_match('~^https://[^\s]+$~i', $analyticsScriptUrl) !== 1)) {
            AdminSecurity::flash('error', 'Analytics requires an HTTPS script URL (for example https://plausible.io/js/script.js or https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX).');
            Response::redirect('/admin/settings');
        }
        foreach ($allowed as $key) {
            if (mb_strlen(trim((string) $request->input($key, ''))) > 2000) {
                AdminSecurity::flash('error', 'One or more settings are too long. Please shorten the values and try again.');
                Response::redirect('/admin/settings');
            }
        }
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO site_settings (setting_key, setting_value, value_type, setting_group, is_public, updated_by) VALUES (:key,:value,:type,:group_name,1,:user_id) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value), updated_by=VALUES(updated_by)');
        $seoKeys = ['default_meta_title', 'default_meta_description', 'default_author', 'default_social_image', 'google_site_verification'];
        $urlKeys = ['site_url', 'default_social_image', 'analytics_script_url', 'founder_linkedin', ...$socialKeys];
        foreach ($allowed as $key) {
            $value = $normalized[$key] ?? trim((string) $request->input($key, ''));
            $group = in_array($key, $contactKeys, true) ? 'contact' : (in_array($key, $whatsAppApiKeys, true) ? 'whatsapp' : (in_array($key, $seoKeys, true) || str_contains($key, 'meta') ? 'seo' : (str_starts_with($key, 'social_') ? 'social' : (str_starts_with($key, 'analytics_') ? 'analytics' : 'branding'))));
            $type = in_array($key, ['contact_email', 'support_email'], true) ? 'email' : (in_array($key, $urlKeys, true) ? 'url' : (in_array($key, ['business_latitude', 'business_longitude'], true) ? 'number' : 'string'));
            $stmt->execute(['key' => $key, 'value' => $value, 'type' => $type, 'group_name' => $group, 'user_id' => $user['id']]);
        }
        $this->audit('settings.updated', 'site_settings', null, ['keys' => $allowed]);
        AdminSecurity::flash('success', 'Website settings saved.');
        Response::redirect('/admin/settings');
    }

    private function render(string $view, array $data): void
    {
        $data['csrf'] = AdminSecurity::csrfToken();
        $data['adminUser'] = AdminSecurity::user();
        $data['flash'] = AdminSecurity::pullFlash();
        Response::html(View::render('admin/' . $view, $data, 'layouts/admin'));
    }

    private function audit(string $action, ?string $type, ?string $id, array $details): void
    {
        try {
            $user = AdminSecurity::user();
            Database::connection()->prepare('INSERT INTO admin_audit_logs (admin_user_id, action, entity_type, entity_id, details, ip_address) VALUES (:user_id,:action,:type,:entity_id,:details,:ip)')->execute([
                'user_id' => $user['id'] ?? null,
                'action' => $action,
                'type' => $type,
                'entity_id' => $id,
                'details' => json_encode($details, JSON_UNESCAPED_SLASHES),
                'ip' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
            ]);
        } catch (Throwable $e) {
            error_log('[AlbaTech audit] ' . $e->getMessage());
        }
    }

    private function emptyService(): array
    {
        return ['id' => '', 'category_id' => '', 'name' => '', 'slug' => '', 'short_description' => '', 'description' => '', 'who_for' => '', 'what_included' => '', 'what_needed' => '', 'pricing_type' => 'quote', 'pricing_amount' => '', 'turnaround' => '', 'meta_title' => '', 'meta_description' => '', 'status' => 'draft', 'is_featured' => 0, 'sort_order' => 0];
    }
}
