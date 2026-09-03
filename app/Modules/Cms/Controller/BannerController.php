<?php

declare(strict_types=1);

namespace App\Modules\Cms\Controller;

use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Admin\Service\MediaService;
use App\Modules\Cms\Repository\BannerRepository;
use App\Modules\Cms\Service\BannerService;

final class BannerController extends BaseController
{
    public function __construct(
        private readonly BannerService $bannerService,
        private readonly BannerRepository $banners,
        private readonly MediaService $mediaService
    ) {
    }

    public function index(Request $request): Response
    {
        return $this->view('admin.banners.index', ['banners' => $this->banners->allForAdmin()]);
    }

    public function create(Request $request): Response
    {
        return $this->view('admin.banners.form', ['banner' => null]);
    }

    public function store(Request $request): Response
    {
        $data = $request->all();
        $data['media_id'] = $this->handleImageUpload($request);

        $id = $this->bannerService->create($data);
        Session::flash('_success', 'Banner created.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/banners/' . $id . '/edit');
    }

    public function edit(Request $request): Response
    {
        $banner = $this->banners->find((int) $request->param('id'));

        if (!$banner) {
            return Response::text('Not found', 404);
        }

        return $this->view('admin.banners.form', ['banner' => $banner]);
    }

    public function update(Request $request): Response
    {
        $id = (int) $request->param('id');
        $existing = $this->banners->find($id);
        $data = $request->all();

        $uploadedMediaId = $this->handleImageUpload($request);
        // Keep the current image unless a new one was actually uploaded —
        // otherwise every edit that doesn't touch the file field would
        // silently clear the banner's image.
        $data['media_id'] = $uploadedMediaId ?: ($existing['media_id'] ?? null);

        $this->bannerService->update($id, $data);
        Session::flash('_success', 'Banner updated.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/banners/' . $id . '/edit');
    }

    public function destroy(Request $request): Response
    {
        $this->bannerService->delete((int) $request->param('id'));
        Session::flash('_success', 'Banner deleted.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/banners');
    }

    private function handleImageUpload(Request $request): ?int
    {
        $file = $request->file('image');

        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }

        $result = $this->mediaService->storeUpload($file, 'banner');

        return $result['success'] ? $result['id'] : null;
    }
}
