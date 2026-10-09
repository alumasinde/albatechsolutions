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
                $stmt = Database::connection()->prepare("SELECT id, first_name, last_name, email, password_hash FROM admin_users WHERE email = :email AND status = 'active' LIMIT 1");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user && password_verify($password, (string) $user['password_hash'])) {
                    session_regenerate_id(true);
                    $_SESSION['admin_user'] = ['id' => (int) $user['id'], 'first_name' => $user['first_name'], 'last_name' => $user['last_name'], 'email' => $user['email']];
                    Database::connection()->prepare('UPDATE admin_users SET last_login_at = NOW() WHERE id = :id')->execute(['id' => $user['id']]);
                    $this->audit('login', 'admin_user', (string) $user['id'], []);
                    Response::redirect('/admin');
                }
                $error = 'Email or password is incorrect.';
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
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], '', (bool) $params['secure'], (bool) $params['httponly']);
        }
        session_destroy();
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
        AdminSecurity::requireUser();
        $stmt = Database::connection()->query('SELECT s.*, c.name AS category_name FROM services s INNER JOIN service_categories c ON c.id = s.category_id ORDER BY s.sort_order, s.name');
        $categories = Database::connection()->query('SELECT id, name, status FROM service_categories ORDER BY sort_order, name')->fetchAll();
        $this->render('services', ['pageTitle' => 'Services', 'services' => $stmt->fetchAll(), 'categories' => $categories]);
    }

    public function serviceCreate(): void
    {
        AdminSecurity::requireUser();
        $categories = Database::connection()->query('SELECT id, name FROM service_categories WHERE status = \'active\' ORDER BY sort_order, name')->fetchAll();
        $this->render('service-form', ['pageTitle' => 'Add service', 'service' => $this->emptyService(), 'categories' => $categories, 'formAction' => '/admin/services/create']);
    }

    public function serviceEdit(Request $request, array $params): void
    {
        AdminSecurity::requireUser();
        $stmt = Database::connection()->prepare('SELECT * FROM services WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => (int) ($params['id'] ?? 0)]);
        $service = $stmt->fetch();
        if (!$service) { AdminSecurity::flash('error', 'Service not found.'); Response::redirect('/admin/services'); }
        $categories = Database::connection()->query('SELECT id, name FROM service_categories ORDER BY sort_order, name')->fetchAll();
        $this->render('service-form', ['pageTitle' => 'Edit service', 'service' => $service, 'categories' => $categories, 'formAction' => '/admin/services/' . (int) $service['id'] . '/edit']);
    }

    public function serviceSave(Request $request, array $params = []): void
    {
        $user = AdminSecurity::requireUser();
        if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) { AdminSecurity::flash('error', 'Your session expired. Please submit the form again.'); Response::redirect('/admin/services'); }
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
        if ($slug === '' || mb_strlen($slug) > 200) $errors[] = 'Enter a valid URL slug.';
        if ($categoryId < 1) $errors[] = 'Choose a category.';
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
            'category_id' => $categoryId, 'name' => $name, 'slug' => $slug,
            'short_description' => trim((string) $request->input('short_description', '')),
            'description' => trim((string) $request->input('description', '')),
            'who_for' => trim((string) $request->input('who_for', '')),
            'what_included' => trim((string) $request->input('what_included', '')),
            'what_needed' => trim((string) $request->input('what_needed', '')),
            'pricing_type' => $pricingType, 'pricing_amount' => $amount,
            'pricing_label' => $pricingType === 'quote' ? 'Contact for price' : null,
            'meta_title' => trim((string) $request->input('meta_title', '')) ?: null,
            'meta_description' => trim((string) $request->input('meta_description', '')) ?: null,
            'status' => $status, 'is_featured' => $request->input('is_featured') === '1' ? 1 : 0,
            'sort_order' => max(0, (int) $request->input('sort_order', 0)),
        ];
        $pdo = Database::connection();
        try {
            if ($id > 0) {
                $data['id'] = $id;
                $sql = 'UPDATE services SET category_id=:category_id, name=:name, slug=:slug, short_description=:short_description, description=:description, who_for=:who_for, what_included=:what_included, what_needed=:what_needed, pricing_type=:pricing_type, pricing_amount=:pricing_amount, pricing_label=:pricing_label, meta_title=:meta_title, meta_description=:meta_description, status=:status, is_featured=:is_featured, sort_order=:sort_order WHERE id=:id';
                $pdo->prepare($sql)->execute($data);
                $this->audit('service.updated', 'service', (string) $id, ['name' => $name]);
                AdminSecurity::flash('success', 'Service updated successfully.');
            } else {
                $sql = 'INSERT INTO services (category_id,name,slug,short_description,description,who_for,what_included,what_needed,pricing_type,pricing_amount,pricing_label,meta_title,meta_description,status,is_featured,sort_order) VALUES (:category_id,:name,:slug,:short_description,:description,:who_for,:what_included,:what_needed,:pricing_type,:pricing_amount,:pricing_label,:meta_title,:meta_description,:status,:is_featured,:sort_order)';
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
        $user = AdminSecurity::requireUser();
        if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) { AdminSecurity::flash('error', 'Your session expired. Try again.'); Response::redirect('/admin/services'); }
        $id = (int) ($params['id'] ?? 0);
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT name, status FROM services WHERE id = :id'); $stmt->execute(['id' => $id]); $service = $stmt->fetch();
        if (!$service) { AdminSecurity::flash('error', 'Service not found.'); Response::redirect('/admin/services'); }
        $next = $service['status'] === 'published' ? 'draft' : 'published';
        $pdo->prepare('UPDATE services SET status = :status WHERE id = :id')->execute(['status' => $next, 'id' => $id]);
        $this->audit('service.visibility_changed', 'service', (string) $id, ['status' => $next]);
        AdminSecurity::flash('success', $next === 'published' ? 'Service is now live.' : 'Service is now hidden from the public website.');
        Response::redirect('/admin/services');
    }

    public function serviceFeature(Request $request, array $params): void
    {
        AdminSecurity::requireUser();
        if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) { AdminSecurity::flash('error', 'Your session expired. Try again.'); Response::redirect('/admin/services'); }
        $id = (int) ($params['id'] ?? 0); $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT name, is_featured FROM services WHERE id = :id'); $stmt->execute(['id' => $id]); $service = $stmt->fetch();
        if (!$service) { AdminSecurity::flash('error', 'Service not found.'); Response::redirect('/admin/services'); }
        $next = (int) $service['is_featured'] === 1 ? 0 : 1;
        $pdo->prepare('UPDATE services SET is_featured = :featured WHERE id = :id')->execute(['featured' => $next, 'id' => $id]);
        $this->audit('service.featured_changed', 'service', (string) $id, ['is_featured' => $next]);
        AdminSecurity::flash('success', $next ? 'Service will be featured on the homepage when published.' : 'Service removed from homepage features.');
        Response::redirect('/admin/services');
    }

    public function categories(): void
    {
        AdminSecurity::requireUser();
        $categories = Database::connection()->query('SELECT c.*, (SELECT COUNT(*) FROM services s WHERE s.category_id = c.id) AS service_count FROM service_categories c ORDER BY c.sort_order, c.name')->fetchAll();
        $this->render('categories', ['pageTitle' => 'Categories', 'categories' => $categories]);
    }

    public function categorySave(Request $request, array $params = []): void
    {
        AdminSecurity::requireUser();
        if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) { AdminSecurity::flash('error', 'Your session expired. Try again.'); Response::redirect('/admin/categories'); }
        $id = (int) ($params['id'] ?? 0); $name = trim((string) $request->input('name', ''));
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', (string) ($request->input('slug') ?: $name)) ?? '', '-'));
        $description = trim((string) $request->input('description', ''));
        $status = $request->input('status') === 'inactive' ? 'inactive' : 'active';
        if ($name === '' || $slug === '') { AdminSecurity::flash('error', 'Category name and slug are required.'); Response::redirect('/admin/categories'); }
        try {
            if ($id) Database::connection()->prepare('UPDATE service_categories SET name=:name, slug=:slug, description=:description, status=:status, sort_order=:sort_order WHERE id=:id')->execute(['name'=>$name,'slug'=>$slug,'description'=>$description,'status'=>$status,'sort_order'=>max(0,(int)$request->input('sort_order',0)),'id'=>$id]);
            else Database::connection()->prepare('INSERT INTO service_categories (name,slug,description,status,sort_order) VALUES (:name,:slug,:description,:status,:sort_order)')->execute(['name'=>$name,'slug'=>$slug,'description'=>$description,'status'=>$status,'sort_order'=>max(0,(int)$request->input('sort_order',0))]);
            AdminSecurity::flash('success', 'Category saved.');
        } catch (Throwable $e) { AdminSecurity::flash('error', 'Could not save category. Check that its slug is unique.'); }
        Response::redirect('/admin/categories');
    }

    public function settings(): void
    {
        AdminSecurity::requireUser();
        $rows = Database::connection()->query('SELECT setting_key, setting_value, setting_group FROM site_settings ORDER BY setting_group, setting_key')->fetchAll();
        $settings = []; foreach ($rows as $row) $settings[$row['setting_key']] = $row;
        $this->render('settings', ['pageTitle' => 'Site settings', 'settings' => $settings]);
    }

    public function settingsSave(Request $request): void
    {
        $user = AdminSecurity::requireUser();
        if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) { AdminSecurity::flash('error', 'Your session expired. Try again.'); Response::redirect('/admin/settings'); }
        $allowed = ['site_name','site_url','default_author','default_meta_description','default_meta_keywords','whatsapp_number','contact_email','business_phone','business_address'];
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO site_settings (setting_key, setting_value, value_type, setting_group, is_public, updated_by) VALUES (:key,:value,:type,:group_name,1,:user_id) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value), updated_by=VALUES(updated_by)');
        foreach ($allowed as $key) {
            $value = trim((string) $request->input($key, ''));
            $group = in_array($key, ['whatsapp_number','contact_email','business_phone','business_address'], true) ? 'contact' : (str_contains($key, 'meta') || $key === 'default_author' ? 'seo' : 'branding');
            $type = $key === 'contact_email' ? 'email' : ($key === 'site_url' ? 'url' : 'string');
            $stmt->execute(['key'=>$key,'value'=>$value,'type'=>$type,'group_name'=>$group,'user_id'=>$user['id']]);
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
                'user_id' => $user['id'] ?? null, 'action' => $action, 'type' => $type, 'entity_id' => $id,
                'details' => json_encode($details, JSON_UNESCAPED_SLASHES), 'ip' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
            ]);
        } catch (Throwable $e) { error_log('[AlbaTech audit] ' . $e->getMessage()); }
    }

    private function emptyService(): array
    {
        return ['id'=>'','category_id'=>'','name'=>'','slug'=>'','short_description'=>'','description'=>'','who_for'=>'','what_included'=>'','what_needed'=>'','pricing_type'=>'quote','pricing_amount'=>'','meta_title'=>'','meta_description'=>'','status'=>'draft','is_featured'=>0,'sort_order'=>0];
    }
}
