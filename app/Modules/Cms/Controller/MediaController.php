<?php

declare(strict_types=1);

namespace App\Modules\Cms\Controller;

use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Admin\Service\MediaService;
use App\Modules\Cms\Repository\MediaRepository;

final class MediaController extends BaseController
{
    public function __construct(
        private readonly MediaRepository $media,
        private readonly MediaService $mediaService
    ) {
    }

    public function index(Request $request): Response
    {
        $page = max(1, (int) $request->input('page', 1));

        return $this->view('admin.media.index', [
            'media' => $this->media->paginate($page),
            'page' => $page,
        ]);
    }

    public function store(Request $request): Response
    {
        $file = $request->file('file');

        if (!$file) {
            Session::flash('_errors', ['file' => ['Please choose a file.']]);

            return $this->back();
        }

        $result = $this->mediaService->storeUpload($file, (string) $request->input('purpose', 'library'));

        if (!$result['success']) {
            Session::flash('_errors', ['file' => [$result['message']]]);

            return $this->back();
        }

        Session::flash('_success', 'File uploaded.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/media');
    }

    public function destroy(Request $request): Response
    {
        $this->media->delete((int) $request->param('id'));
        Session::flash('_success', 'File removed.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/media');
    }
}
