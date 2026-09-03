<?php

declare(strict_types=1);

namespace App\Modules\Cms\Controller;

use App\Core\BaseController;
use App\Core\Config;
use App\Core\Helpers\Sanitizer;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Cms\Repository\ServiceCategoryRepository;
use App\Modules\Cms\Repository\ServiceRepository;
use App\Modules\Cms\Service\ServiceCatalogueService;

final class ServiceController extends BaseController
{
    public function __construct(
        private readonly ServiceCatalogueService $catalogueService,
        private readonly ServiceRepository $services,
        private readonly ServiceCategoryRepository $categories
    ) {
    }

    public function index(Request $request): Response
    {
        return $this->view('admin.services.index', ['services' => $this->services->allForAdmin()]);
    }

    public function create(Request $request): Response
    {
        return $this->view('admin.services.form', [
            'service' => null,
            'commerce' => null,
            'categories' => $this->categories->allActive(),
        ]);
    }

    public function store(Request $request): Response
    {
        if (trim((string) $request->input('name', '')) === '') {
            Session::flash('_errors', ['name' => ['Service name is required.']]);

            return $this->back();
        }

        $id = $this->catalogueService->create($request->all());
        Session::flash('_success', 'Service created.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/services/' . $id . '/edit');
    }

    public function edit(Request $request): Response
    {
        $service = $this->services->find((int) $request->param('id'));

        if (!$service) {
            return Response::text('Not found', 404);
        }

        return $this->view('admin.services.form', [
            'service' => $service,
            'commerce' => $this->services->findCommerce((int) $service['id']),
            'categories' => $this->categories->allActive(),
        ]);
    }

    public function update(Request $request): Response
    {
        $id = (int) $request->param('id');

        if (trim((string) $request->input('name', '')) === '') {
            Session::flash('_errors', ['name' => ['Service name is required.']]);

            return $this->back();
        }

        $this->catalogueService->update($id, $request->all());
        Session::flash('_success', 'Service updated.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/services/' . $id . '/edit');
    }

    public function toggleStatus(Request $request): Response
    {
        $id = (int) $request->param('id');

        try {
            $status = $this->catalogueService->toggleStatus($id);
        } catch (\RuntimeException) {
            Session::flash('_errors', ['service' => ['Service not found.']]);

            return $this->redirect(Config::get('admin.path', '/admin') . '/services');
        }

        Session::flash('_success', $status === 'published'
            ? 'Service activated and now visible on the website.'
            : 'Service deactivated and hidden from the website.'
        );

        return $this->redirect(Config::get('admin.path', '/admin') . '/services');
    }

    public function toggleHomepage(Request $request): Response
    {
        $id = (int) $request->param('id');

        try {
            $enabled = $this->catalogueService->toggleHomepage($id);
        } catch (\RuntimeException $e) {
            Session::flash('_errors', ['service' => [$e->getMessage()]]);

            return $this->redirect(Config::get('admin.path', '/admin') . '/services');
        }

        Session::flash('_success', $enabled
            ? 'Service added to the homepage.'
            : 'Service removed from the homepage.'
        );

        return $this->redirect(Config::get('admin.path', '/admin') . '/services');
    }

    public function destroy(Request $request): Response
    {
        $this->catalogueService->delete((int) $request->param('id'));
        Session::flash('_success', 'Service deleted.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/services');
    }

    public function storeCategory(Request $request): Response
    {
        $name = trim((string) $request->input('name', ''));

        if ($name === '') {
            Session::flash('_errors', ['name' => ['Category name is required.']]);

            return $this->back();
        }

        $this->categories->create([
            'name' => $name,
            'slug' => Sanitizer::slug($name),
        ]);

        Session::flash('_success', 'Category added.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/services');
    }
}
