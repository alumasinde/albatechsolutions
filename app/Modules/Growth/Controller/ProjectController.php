<?php

declare(strict_types=1);

namespace App\Modules\Growth\Controller;

use App\Core\BaseController;
use App\Core\Config;
use App\Core\Helpers\Sanitizer;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Growth\Repository\ProjectRepository;

final class ProjectController extends BaseController
{
    public function __construct(private readonly ProjectRepository $projects) {}

    public function index(Request $request): Response { return $this->view('admin.projects.index', ['projects' => $this->projects->allForAdmin()]); }

    public function create(Request $request): Response { return $this->view('admin.projects.form', ['project' => null]); }

    public function store(Request $request): Response
    {
        $data = $this->payload($request);
        if ($data['title'] === '' || $data['summary'] === '') { Session::flash('_errors', ['title' => ['Title and summary are required.']]); return $this->back(); }
        $id = $this->projects->create($data);
        Session::flash('_success', 'Project created.');
        return $this->redirect(Config::get('admin.path','/admin') . '/projects/' . $id . '/edit');
    }

    public function edit(Request $request): Response
    {
        $project = $this->projects->find((int)$request->param('id'));
        if (!$project) return Response::text('Not found', 404);
        return $this->view('admin.projects.form', ['project' => $project]);
    }

    public function update(Request $request): Response
    {
        $id = (int)$request->param('id');
        $data = $this->payload($request, $id);
        $this->projects->update($id, $data);
        Session::flash('_success', 'Project updated.');
        return $this->redirect(Config::get('admin.path','/admin') . '/projects/' . $id . '/edit');
    }

    public function destroy(Request $request): Response
    {
        $this->projects->delete((int)$request->param('id'));
        Session::flash('_success', 'Project removed.');
        return $this->redirect(Config::get('admin.path','/admin') . '/projects');
    }

    private function payload(Request $request, ?int $id = null): array
    {
        $title = trim((string)$request->input('title',''));
        $status = $request->input('status','draft') === 'published' ? 'published' : 'draft';
        return [
            'title' => $title,
            'slug' => Sanitizer::slug($title),
            'client_name' => trim((string)$request->input('client_name','')) ?: null,
            'industry' => trim((string)$request->input('industry','')) ?: null,
            'location' => trim((string)$request->input('location','')) ?: null,
            'summary' => trim((string)$request->input('summary','')),
            'description' => (string)$request->input('description',''),
            'challenge' => (string)$request->input('challenge',''),
            'solution' => (string)$request->input('solution',''),
            'results' => (string)$request->input('results',''),
            'technologies' => trim((string)$request->input('technologies','')) ?: null,
            'image_path' => trim((string)$request->input('image_path','')) ?: null,
            'project_url' => trim((string)$request->input('project_url','')) ?: null,
            'featured' => $request->input('featured') ? 1 : 0,
            'status' => $status,
            'meta_title' => trim((string)$request->input('meta_title','')) ?: null,
            'meta_description' => trim((string)$request->input('meta_description','')) ?: null,
            'sort_order' => (int)$request->input('sort_order',0),
            'published_at' => $status === 'published' ? date('Y-m-d H:i:s') : null,
        ];
    }
}
