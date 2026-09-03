<?php

declare(strict_types=1);

namespace App\Modules\Cms\Controller;

use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Admin\Service\MediaService;
use App\Modules\Cms\Repository\TestimonialRepository;
use App\Modules\Cms\Service\TestimonialService;

final class TestimonialController extends BaseController
{
    public function __construct(
        private readonly TestimonialService $testimonialService,
        private readonly TestimonialRepository $testimonials,
        private readonly MediaService $mediaService
    ) {
    }

    public function index(Request $request): Response
    {
        return $this->view('admin.testimonials.index', ['testimonials' => $this->testimonials->allForAdmin()]);
    }

    public function store(Request $request): Response
    {
        if (trim((string) $request->input('client_name', '')) === '' || trim((string) $request->input('quote', '')) === '') {
            Session::flash('_errors', ['client_name' => ['Client name and quote are required.']]);

            return $this->back();
        }

        $data = $request->all();
        $data['photo_media_id'] = $this->handlePhotoUpload($request);

        $this->testimonialService->create($data);
        Session::flash('_success', 'Testimonial added.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/testimonials');
    }

    public function update(Request $request): Response
    {
        $id = (int) $request->param('id');
        $existing = $this->testimonials->find($id);
        $data = $request->all();

        $uploadedMediaId = $this->handlePhotoUpload($request);
        // Keep the current photo unless a new one was actually uploaded.
        $data['photo_media_id'] = $uploadedMediaId ?: ($existing['photo_media_id'] ?? null);

        $this->testimonialService->update($id, $data);
        Session::flash('_success', 'Testimonial updated.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/testimonials');
    }

    public function destroy(Request $request): Response
    {
        $this->testimonialService->delete((int) $request->param('id'));
        Session::flash('_success', 'Testimonial deleted.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/testimonials');
    }

    private function handlePhotoUpload(Request $request): ?int
    {
        $file = $request->file('photo');

        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }

        $result = $this->mediaService->storeUpload($file, 'testimonial');

        return $result['success'] ? $result['id'] : null;
    }
}
