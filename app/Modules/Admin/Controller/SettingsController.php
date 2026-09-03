<?php

declare(strict_types=1);

namespace App\Modules\Admin\Controller;

use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Settings;
use App\Modules\Admin\Service\MediaService;
use App\Modules\Admin\Service\SettingsService;

final class SettingsController extends BaseController
{
    public function __construct(
        private readonly SettingsService $settingsService,
        private readonly MediaService $mediaService
    ) {
    }

    public function edit(Request $request): Response
    {
        return $this->view('admin.settings.edit', ['settings' => Settings::all()]);
    }

    public function update(Request $request): Response
    {
        $this->settingsService->update($request->all());

        $logo = $request->file('logo');
        if ($logo && ($logo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $result = $this->mediaService->storeUpload($logo, 'logo');

            if ($result['success']) {
                Settings::set('site_logo_path', $result['path']);
            } else {
                Session::flash('_errors', ['logo' => [$result['message']]]);
            }
        }

        Session::flash('_success', 'Settings updated.');

        return $this->redirect($request->input('_return', Config::get('admin.path', '/admin') . '/settings'));
    }
}
