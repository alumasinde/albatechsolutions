<?php

declare(strict_types=1);

namespace App\Modules\Cms\Controller;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Cms\Repository\PageRepository;
use App\Modules\Cms\Service\PageService;

final class PageController extends BaseController
{
    public function __construct(
        private readonly PageService $pageService,
        private readonly PageRepository $pages
    ) {
    }

    public function index(Request $request): Response
    {
        return $this->view('admin.pages.index', ['pages' => $this->pages->allForAdmin()]);
    }

    public function create(Request $request): Response
    {
        return $this->view('admin.pages.form', ['page' => null]);
    }

    public function store(Request $request): Response
    {
        if (trim((string) $request->input('title', '')) === '') {
            Session::flash('_errors', ['title' => ['Title is required.']]);

            return $this->back();
        }

        $id = $this->pageService->create($request->all(), (int) Auth::id());
        Session::flash('_success', 'Page created.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/pages/' . $id . '/edit');
    }

    public function edit(Request $request): Response
    {
        $page = $this->pages->find((int) $request->param('id'));

        if (!$page) {
            return Response::text('Not found', 404);
        }

        return $this->view('admin.pages.form', ['page' => $page]);
    }

    public function update(Request $request): Response
    {
        $id = (int) $request->param('id');

        if (trim((string) $request->input('title', '')) === '') {
            Session::flash('_errors', ['title' => ['Title is required.']]);

            return $this->back();
        }

        $this->pageService->update($id, $request->all());
        Session::flash('_success', 'Page updated.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/pages/' . $id . '/edit');
    }

    public function destroy(Request $request): Response
    {
        $this->pageService->delete((int) $request->param('id'));
        Session::flash('_success', 'Page deleted.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/pages');
    }
}
