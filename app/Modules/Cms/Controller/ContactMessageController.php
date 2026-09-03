<?php

declare(strict_types=1);

namespace App\Modules\Cms\Controller;

use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Cms\Repository\ContactMessageRepository;

final class ContactMessageController extends BaseController
{
    public function __construct(
        private readonly ContactMessageRepository $messages
    ) {
    }

    public function index(Request $request): Response
    {
        return $this->view('admin.contact-messages.index', [
            'messages' => $this->messages->allForAdmin(),
        ]);
    }

    public function markRead(Request $request): Response
    {
        $this->messages->update((int) $request->param('id'), ['status' => 'read']);
        Session::flash('_success', 'Marked as read.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/contact-messages');
    }
}
