<?php
declare(strict_types=1);

namespace AlbaTech\Controllers;

use AlbaTech\Core\AdminSecurity;
use AlbaTech\Core\Database;
use AlbaTech\Core\Request;
use AlbaTech\Core\Response;
use AlbaTech\Core\View;
use DOMDocument;
use DOMElement;
use DOMNode;

final class GuideAdminController
{
    public function index(): void
    {
        AdminSecurity::requirePermission('services.manage');
        $guides = Database::connection()->query('SELECT id,title,slug,category,status,updated_at FROM guides ORDER BY updated_at DESC')->fetchAll();
        $this->render('guides-admin', ['pageTitle' => 'Guides & articles', 'guides' => $guides]);
    }

    public function create(): void
    {
        AdminSecurity::requirePermission('services.manage');
        $this->form([]);
    }

    public function edit(Request $request, array $params): void
    {
        AdminSecurity::requirePermission('services.manage');
        $stmt = Database::connection()->prepare('SELECT * FROM guides WHERE id=:id LIMIT 1');
        $stmt->execute(['id' => (int)($params['id'] ?? 0)]);
        $guide = $stmt->fetch();
        if (!$guide) {
            AdminSecurity::flash('error', 'Guide not found.');
            Response::redirect('/admin/guides');
        }
        $this->form($guide);
    }

    public function save(Request $request, array $params = []): void
    {
        $user = AdminSecurity::requirePermission('services.manage');
        if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) {
            AdminSecurity::flash('error', 'Your session expired. Please try again.');
            Response::redirect('/admin/guides');
        }
        $id = (int)($params['id'] ?? 0);
        $title = trim((string)$request->input('title',''));
        $slug = strtolower(trim((string)$request->input('slug','')));
        $slug = trim((string)preg_replace('/[^a-z0-9]+/', '-', $slug), '-');
        $category = trim((string)$request->input('category','General'));
        $summary = trim((string)$request->input('summary',''));
        $intro = trim((string)$request->input('intro',''));
        $metaTitle = trim((string)$request->input('meta_title',''));
        $metaDescription = trim((string)$request->input('meta_description',''));
        $status = (string)$request->input('status','draft');
        $raw = (string)$request->input('content_html','');
        $content = self::sanitizeHtml($raw);
        $errors = [];
        if ($title === '' || mb_strlen($title) > 220) $errors[] = 'Enter a title (maximum 220 characters).';
        if ($slug === '' || mb_strlen($slug) > 220) $errors[] = 'Enter a valid URL slug.';
        if ($category === '' || mb_strlen($category) > 100) $errors[] = 'Enter a category (maximum 100 characters).';
        if ($summary === '' || mb_strlen($summary) > 500) $errors[] = 'Enter a short summary (maximum 500 characters).';
        if ($intro === '' || mb_strlen($intro) > 5000) $errors[] = 'Enter an introduction (maximum 5,000 characters).';
        if (trim(strip_tags($content)) === '') $errors[] = 'Write some article content.';
        if (!in_array($status, ['draft','published','archived'], true)) $errors[] = 'Choose a valid publication status.';
        if (mb_strlen($metaTitle) > 255 || mb_strlen($metaDescription) > 320) $errors[] = 'SEO title or description is too long.';
        $pdo = Database::connection();
        $dupe = $pdo->prepare('SELECT id FROM guides WHERE slug=:slug AND id<>:id LIMIT 1');
        $dupe->execute(['slug'=>$slug,'id'=>$id]);
        if ($dupe->fetchColumn()) $errors[] = 'That URL slug is already in use.';
        $guide = compact('id','title','slug','category','summary','intro','metaTitle','metaDescription','status');
        $guide['meta_title']=$metaTitle; $guide['meta_description']=$metaDescription; $guide['content_html']=$content;
        if ($errors) {
            $this->form($guide, $errors);
            return;
        }
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE guides SET title=:title,slug=:slug,category=:category,summary=:summary,intro=:intro,content_html=:content_html,meta_title=:meta_title,meta_description=:meta_description,status=:status,published_at=IF(:status='published',COALESCE(published_at,NOW()),published_at),updated_by=:user WHERE id=:id");
            $stmt->execute(['title'=>$title,'slug'=>$slug,'category'=>$category,'summary'=>$summary,'intro'=>$intro,'content_html'=>$content,'meta_title'=>$metaTitle ?: null,'meta_description'=>$metaDescription ?: null,'status'=>$status,'user'=>$user['id'],'id'=>$id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO guides (title,slug,category,summary,intro,content_html,meta_title,meta_description,status,published_at,created_by,updated_by) VALUES (:title,:slug,:category,:summary,:intro,:content_html,:meta_title,:meta_description,:status,IF(:status='published',NOW(),NULL),:user,:user)");
            $stmt->execute(['title'=>$title,'slug'=>$slug,'category'=>$category,'summary'=>$summary,'intro'=>$intro,'content_html'=>$content,'meta_title'=>$metaTitle ?: null,'meta_description'=>$metaDescription ?: null,'status'=>$status,'user'=>$user['id']]);
        }
        AdminSecurity::flash('success', 'Guide saved successfully.');
        Response::redirect('/admin/guides');
    }

    public function delete(Request $request, array $params): void
    {
        AdminSecurity::requirePermission('services.manage');
        if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) {
            AdminSecurity::flash('error', 'Your session expired. Please try again.');
            Response::redirect('/admin/guides');
        }
        Database::connection()->prepare('DELETE FROM guides WHERE id=:id')->execute(['id'=>(int)($params['id'] ?? 0)]);
        AdminSecurity::flash('success', 'Guide deleted.');
        Response::redirect('/admin/guides');
    }

    private function form(array $guide, array $errors = []): void
    {
        $defaults = ['id'=>'','title'=>'','slug'=>'','category'=>'General','summary'=>'','intro'=>'','content_html'=>'','meta_title'=>'','meta_description'=>'','status'=>'draft'];
        $this->render('guide-form', ['pageTitle'=>empty($guide['id'])?'Write a guide':'Edit guide','guide'=>array_merge($defaults,$guide),'errors'=>$errors]);
    }

    private function render(string $view, array $data): void
    {
        $data['csrf'] = AdminSecurity::csrfToken();
        $data['adminUser'] = AdminSecurity::user();
        $data['flash'] = AdminSecurity::pullFlash();
        Response::html(View::render('admin/'.$view,$data,'layouts/admin'));
    }

    /** Allow-list HTML elements and attributes; strip scripts, event handlers and unsafe URLs. */
    public static function sanitizeHtml(string $html): string
    {
        if (!class_exists(DOMDocument::class)) return htmlspecialchars(strip_tags($html), ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');
        $allowed = ['p','br','h1','h2','h3','h4','h5','h6','strong','b','em','i','u','s','ul','ol','li','blockquote','pre','code','hr','a','img','table','thead','tbody','tr','th','td','figure','figcaption','div','span'];
        $doc = new DOMDocument('1.0','UTF-8');
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8"?><div id="guide-root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        $root = $doc->getElementById('guide-root');
        if (!$root) return '';
        $clean = function(DOMNode $node) use (&$clean,$allowed): void {
            for ($child=$node->firstChild; $child;) {
                $next=$child->nextSibling;
                if ($child instanceof DOMElement) {
                    $tag=strtolower($child->tagName);
                    if (!in_array($tag,$allowed,true)) {
                        if (in_array($tag,['script','style','iframe','object','embed','form','input','svg','math'],true)) $node->removeChild($child);
                        else { while($child->firstChild) $node->insertBefore($child->firstChild,$child); $node->removeChild($child); }
                    } else {
                        $attrs=[];
                        foreach ($child->attributes as $attr) $attrs[]=$attr->name;
                        foreach($attrs as $attr) if (!in_array(strtolower($attr),['href','src','alt','title','target','rel','colspan','rowspan','scope','class','style'],true)) $child->removeAttribute($attr);
                        foreach(['href','src'] as $attr) if ($child->hasAttribute($attr)) {
                            $url=trim($child->getAttribute($attr));
                            if (preg_match('~^(?:https://|http://|/|#)~i',$url)!==1 || preg_match('~^(?:javascript|data|vbscript):~i',$url)) $child->removeAttribute($attr);
                        }
                        if ($tag==='a' && $child->getAttribute('target')==='_blank') $child->setAttribute('rel','noopener noreferrer');
                        if ($child->hasAttribute('style')) {
                            $style=preg_replace('/[^a-zA-Z0-9#(),.%:;\\s-]/','',$child->getAttribute('style'));
                            if (preg_match('/url\\s*\\(|expression|position\\s*:\\s*fixed/i',$style)) $style='';
                            if ($style==='') $child->removeAttribute('style'); else $child->setAttribute('style',$style);
                        }
                        $clean($child);
                    }
                } elseif (!($child instanceof \DOMText)) $node->removeChild($child);
                $child=$next;
            }
        };
        $clean($root);
        $out=''; foreach($root->childNodes as $child) $out.=$doc->saveHTML($child);
        return $out;
    }
}
