<?php

declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\AdminSecurity;
use AlbaTech\Core\Database;
use AlbaTech\Core\Request;
use AlbaTech\Core\Response;
use AlbaTech\Core\SiteUrl;
use AlbaTech\Core\View;
use AlbaTech\Core\WhatsAppLink;
use AlbaTech\Core\SeoDefaults;
use PDO;
use Throwable;

final class WorkController
{
    public function index(Request $request): void
    {
        $pdo = Database::connection();
        $categories = $pdo->query("SELECT c.id, c.name, c.slug FROM service_categories c WHERE c.status = 'active' AND EXISTS (SELECT 1 FROM work_projects w WHERE w.category_id=c.id AND w.status='published') ORDER BY c.sort_order, c.name")->fetchAll();
        $categorySlug = trim((string) $request->input('category', ''));
        $sql = "SELECT w.*, c.name AS category_name, c.slug AS category_slug FROM work_projects w JOIN service_categories c ON c.id=w.category_id WHERE w.status='published'";
        $params = [];
        if ($categorySlug !== '') { $sql .= ' AND c.slug = :category'; $params['category'] = $categorySlug; }
        $sql .= ' ORDER BY w.sort_order ASC, w.updated_at DESC, w.id DESC';
        $stmt = $pdo->prepare($sql); $stmt->execute($params);
        $base = SiteUrl::base();
        Response::html(View::render('work/index', [
            'pageTitle' => 'Our Work | Websites & Business Projects in Kenya | AlbaTech Solutions',
            'metaDescription' => 'Selected website, business systems and digital projects delivered by AlbaTech Solutions for clients across Kenya.',
            'canonicalUrl' => $base . '/work',
            'metaImage' => $base . '/assets/images/albatech-social-card.png',
            'projects' => $stmt->fetchAll(), 'categories' => $categories, 'selectedCategory' => $categorySlug,
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => '/'],
                ['name' => 'Our Work', 'url' => '/work'],
            ],
        ]));
    }

    public function show(Request $request, array $params): void
    {
        $slug = (string) ($params['slug'] ?? '');
        $stmt = Database::connection()->prepare("SELECT w.*, c.name AS category_name, c.slug AS category_slug FROM work_projects w JOIN service_categories c ON c.id=w.category_id WHERE w.slug=:slug AND w.status='published' LIMIT 1");
        $stmt->execute(['slug' => $slug]); $project = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$project) { Response::html(View::render('errors/404', ['pageTitle'=>'Project not found | AlbaTech Solutions','metaRobots'=>'noindex, nofollow','canonicalUrl'=>'']), 404); return; }
        $base = SiteUrl::base();
        $description = trim((string) ($project['meta_description'] ?: $project['description']));
        $description = preg_replace('/\s+/', ' ', $description) ?? $description;
        if (strlen($description) > 300) $description = substr($description, 0, 297) . '...';
        $contactSettings = (new \AlbaTech\Repositories\SiteSettingsRepository())->publicValues();
        $wa = WhatsAppLink::build($contactSettings, '/get-help?project=' . rawurlencode($project['slug']));
        $relatedStmt = Database::connection()->prepare("SELECT w.id,w.title,w.slug,w.description,w.cover_image,c.name AS category_name FROM work_projects w JOIN service_categories c ON c.id=w.category_id WHERE w.status='published' AND w.id<>:id AND w.category_id=:category ORDER BY w.is_featured DESC,w.sort_order,w.updated_at DESC LIMIT 3");
        $relatedStmt->execute(['id'=>$project['id'],'category'=>$project['category_id']]);
        Response::html(View::render('work/show', [
            'pageTitle' => SeoDefaults::title((string) ($project['meta_title'] ?: ''), (string) $project['title'] . ' | AlbaTech Solutions'),
            'metaDescription' => SeoDefaults::description($description), 'canonicalUrl' => $base . '/work/' . rawurlencode($project['slug']),
            'metaImage' => SeoDefaults::image((string) ($project['cover_image'] ?? ''), $base), 'project' => $project,
            'relatedProjects' => $relatedStmt->fetchAll(), 'whatsAppUrl' => $wa,
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => '/'],
                ['name' => 'Our Work', 'url' => '/work'],
                ['name' => $project['title'], 'url' => ''],
            ],
            'schemaProject' => $project,
        ]));
    }

    public function adminIndex(): void
    {
        AdminSecurity::requirePermission('work.manage');
        $projects = Database::connection()->query('SELECT w.*, c.name AS category_name FROM work_projects w JOIN service_categories c ON c.id=w.category_id ORDER BY w.sort_order,w.updated_at DESC')->fetchAll();
        $this->adminRender('projects', ['pageTitle'=>'Our Work','projects'=>$projects]);
    }

    public function adminCreate(): void
    {
        AdminSecurity::requirePermission('work.manage');
        $categories = Database::connection()->query("SELECT id,name FROM service_categories WHERE status='active' ORDER BY sort_order,name")->fetchAll();
        $this->adminRender('project-form', ['pageTitle'=>'Add project','project'=>$this->emptyProject(),'categories'=>$categories,'formAction'=>'/admin/work/create']);
    }

    public function adminEdit(Request $request, array $params): void
    {
        AdminSecurity::requirePermission('work.manage');
        $stmt = Database::connection()->prepare('SELECT * FROM work_projects WHERE id=:id LIMIT 1'); $stmt->execute(['id'=>(int)($params['id']??0)]); $project=$stmt->fetch();
        if (!$project) { AdminSecurity::flash('error','Project not found.'); Response::redirect('/admin/work'); }
        $categories=Database::connection()->query('SELECT id,name FROM service_categories ORDER BY sort_order,name')->fetchAll();
        $this->adminRender('project-form',['pageTitle'=>'Edit project','project'=>$project,'categories'=>$categories,'formAction'=>'/admin/work/'.(int)$project['id'].'/edit']);
    }

    public function adminSave(Request $request, array $params = []): void
    {
        $user=AdminSecurity::requirePermission('work.manage');
        if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) { AdminSecurity::flash('error','Your session expired. Please submit the form again.'); Response::redirect('/admin/work'); }
        $id=(int)($params['id']??0); $pdo=Database::connection();
        $title=trim((string)$request->input('title','')); $slugInput=trim((string)$request->input('slug',''));
        $slug=strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/','-',$slugInput!==''?$slugInput:$title)??'','-'));
        $description=trim((string)$request->input('description','')); $categoryId=(int)$request->input('category_id',0);
        $client=trim((string)$request->input('client_name','')); $live=trim((string)$request->input('live_url',''));
        $status=(string)$request->input('status','draft'); $featured=$request->input('is_featured')==='1'?1:0; $order=max(0,min(100000,(int)$request->input('sort_order',0)));
        $oldImage=''; if ($id>0) { $q=$pdo->prepare('SELECT cover_image FROM work_projects WHERE id=:id'); $q->execute(['id'=>$id]); $oldImage=(string)($q->fetchColumn()?:''); }
        $image=$oldImage; $errors=[];
        if ($title==='' || strlen($title)>180) $errors[]='Enter a project title of up to 180 characters.';
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/',$slug) || strlen($slug)>200) $errors[]='Use a valid URL slug containing lowercase letters, numbers and hyphens.';
        if ($description==='' || strlen($description)>12000) $errors[]='Enter a project description (up to 12,000 characters).';
        if (!in_array($status,['draft','published','archived'],true)) $errors[]='Choose a valid publication status.';
        $cat=$pdo->prepare("SELECT COUNT(*) FROM service_categories WHERE id=:id AND status='active'"); $cat->execute(['id'=>$categoryId]); if (!(int)$cat->fetchColumn()) $errors[]='Choose an active service category.';
        if ($live!=='' && (!filter_var($live,FILTER_VALIDATE_URL) || !in_array(strtolower((string)parse_url($live,PHP_URL_SCHEME)),['http','https'],true) || strlen($live)>500)) $errors[]='Live link must be a valid HTTP or HTTPS URL.';
        if ($client!=='' && strlen($client)>180) $errors[]='Client name must be 180 characters or fewer.';
        $slugCheck=$pdo->prepare('SELECT id FROM work_projects WHERE slug=:slug AND id<>:id LIMIT 1'); $slugCheck->execute(['slug'=>$slug,'id'=>$id]); if ($slugCheck->fetchColumn()) $errors[]='That URL slug is already in use.';
        $upload=$_FILES['cover_image']??null;
        if (!$errors && is_array($upload) && (int)($upload['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_NO_FILE) {
            $result=$this->storeImage($upload);
            if (isset($result['error'])) $errors[]=$result['error']; else $image=$result['path'];
        }
        if ($image==='') $errors[]='Upload a cover image for this project.';
        if ($errors) { $categories=$pdo->query('SELECT id,name FROM service_categories ORDER BY sort_order,name')->fetchAll(); $project=array_merge($this->emptyProject(),$_POST,['id'=>$id,'slug'=>$slug,'cover_image'=>$image]); $this->adminRender('project-form',['pageTitle'=>$id?'Edit project':'Add project','project'=>$project,'categories'=>$categories,'formAction'=>$id?'/admin/work/'.$id.'/edit':'/admin/work/create','errors'=>$errors]); return; }
        $data=['category_id'=>$categoryId,'title'=>$title,'slug'=>$slug,'description'=>$description,'client_name'=>$client?:null,'live_url'=>$live?:null,'cover_image'=>$image,'status'=>$status,'is_featured'=>$featured,'sort_order'=>$order,'meta_title'=>trim((string)$request->input('meta_title',''))?:null,'meta_description'=>trim((string)$request->input('meta_description',''))?:null];
        try {
            if ($id>0) { $data['id']=$id; $sql='UPDATE work_projects SET category_id=:category_id,title=:title,slug=:slug,description=:description,client_name=:client_name,live_url=:live_url,cover_image=:cover_image,status=:status,is_featured=:is_featured,sort_order=:sort_order,meta_title=:meta_title,meta_description=:meta_description WHERE id=:id'; $pdo->prepare($sql)->execute($data); $action='work.updated'; }
            else { $sql='INSERT INTO work_projects (category_id,title,slug,description,client_name,live_url,cover_image,status,is_featured,sort_order,meta_title,meta_description) VALUES (:category_id,:title,:slug,:description,:client_name,:live_url,:cover_image,:status,:is_featured,:sort_order,:meta_title,:meta_description)'; $pdo->prepare($sql)->execute($data); $id=(int)$pdo->lastInsertId(); $action='work.created'; }
            try { $pdo->prepare('INSERT INTO admin_audit_logs (admin_user_id,action,entity_type,entity_id,details,ip_address) VALUES (:uid,:action,\'work_project\',:eid,:details,:ip)')->execute(['uid'=>$user['id']??null,'action'=>$action,'eid'=>(string)$id,'details'=>json_encode(['title'=>$title,'status'=>$status],JSON_UNESCAPED_SLASHES),'ip'=>substr((string)($_SERVER['REMOTE_ADDR']??''),0,45)]); } catch (Throwable $auditError) { error_log('[AlbaTech Work audit] '.$auditError->getMessage()); }
            if ($oldImage!=='' && $oldImage!==$image) $this->deleteStoredImage($oldImage);
            AdminSecurity::flash('success',$status==='published'?'Project saved and published.':'Project saved.'); Response::redirect('/admin/work');
        } catch (Throwable $e) { error_log('[AlbaTech Work] '.$e->getMessage()); if ($image!==$oldImage) $this->deleteStoredImage($image); AdminSecurity::flash('error','Could not save the project. Check the fields and try again.'); Response::redirect($id?'/admin/work/'.$id.'/edit':'/admin/work/create'); }
    }

    public function adminDelete(Request $request, array $params): void
    {
        AdminSecurity::requirePermission('work.manage'); if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) { AdminSecurity::flash('error','Your session expired. Try again.'); Response::redirect('/admin/work'); }
        $id=(int)($params['id']??0); $pdo=Database::connection(); $q=$pdo->prepare('SELECT cover_image FROM work_projects WHERE id=:id'); $q->execute(['id'=>$id]); $image=(string)($q->fetchColumn()?:'');
        $pdo->prepare('DELETE FROM work_projects WHERE id=:id')->execute(['id'=>$id]); if ($image!=='') $this->deleteStoredImage($image);
        AdminSecurity::flash('success','Project deleted.'); Response::redirect('/admin/work');
    }

    /** @return array{path?:string,error?:string} */
    private function storeImage(array $file): array
    {
        $error=(int)($file['error']??UPLOAD_ERR_NO_FILE); if ($error!==UPLOAD_ERR_OK) return ['error'=>'The cover image upload failed. Please try again.'];
        $tmp=(string)($file['tmp_name']??''); $size=(int)($file['size']??0);
        if ($size<1 || $size>8*1024*1024 || !is_uploaded_file($tmp)) return ['error'=>'Cover images must be valid uploads no larger than 8 MB.'];
        $finfo=new \finfo(FILEINFO_MIME_TYPE); $mime=$finfo->file($tmp);
        $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp']; if (!isset($allowed[$mime])) return ['error'=>'Use a JPG, PNG or WebP image. SVG and other file types are not allowed.'];
        $info=@getimagesize($tmp); if (!$info || (int)$info[0]<1 || (int)$info[1]<1 || (int)$info[0]>12000 || (int)$info[1]>12000) return ['error'=>'The image dimensions are invalid or too large.'];
        $dir=dirname(__DIR__,2).'/public/uploads/work'; if (!is_dir($dir) && !mkdir($dir,0755,true) && !is_dir($dir)) return ['error'=>'Image storage is unavailable. Check folder permissions.'];
        $name=bin2hex(random_bytes(20)); $ext=$allowed[$mime]; $dest=$dir.'/'.$name.'.'.$ext;
        // Re-encode raster uploads when GD is available to strip metadata and reduce size; WebP output is preferred.
        if (function_exists('imagewebp')) {
            $src=$mime==='image/jpeg'?@imagecreatefromjpeg($tmp):($mime==='image/png'?@imagecreatefrompng($tmp):@imagecreatefromwebp($tmp));
            if ($src!==false) {
                $w=imagesx($src); $h=imagesy($src); $max=1800; if (max($w,$h)>$max) { $scale=$max/max($w,$h); $nw=max(1,(int)round($w*$scale)); $nh=max(1,(int)round($h*$scale)); $dst=imagecreatetruecolor($nw,$nh); imagealphablending($dst,false); imagesavealpha($dst,true); imagecopyresampled($dst,$src,0,0,0,0,$nw,$nh,$w,$h); imagedestroy($src); $src=$dst; }
                $dest=$dir.'/'.$name.'.webp'; $ok=@imagewebp($src,$dest,78); imagedestroy($src); if (!$ok) return ['error'=>'Could not optimise this image. Try a different image.']; $ext='webp';
            } else if (!move_uploaded_file($tmp,$dest)) return ['error'=>'Could not store the uploaded image.'];
        } else if (!move_uploaded_file($tmp,$dest)) return ['error'=>'Could not store the uploaded image.'];
        @chmod($dest,0644); return ['path'=>'uploads/work/'.$name.'.'.$ext];
    }

    private function deleteStoredImage(string $path): void
    {
        if (!preg_match('~^uploads/work/[a-f0-9]{40}\.(?:jpg|png|webp)$~',$path)) return;
        $full=dirname(__DIR__,2).'/public/'.$path; if (is_file($full)) @unlink($full);
    }

    private function adminRender(string $view,array $data): void { $data['csrf']=AdminSecurity::csrfToken(); $data['adminUser']=AdminSecurity::user(); $data['flash']=AdminSecurity::pullFlash(); Response::html(View::render('admin/'.$view,$data,'layouts/admin')); }
    private function emptyProject(): array { return ['id'=>'','category_id'=>'','title'=>'','slug'=>'','description'=>'','client_name'=>'','live_url'=>'','cover_image'=>'','status'=>'draft','is_featured'=>0,'sort_order'=>0,'meta_title'=>'','meta_description'=>'']; }
}
