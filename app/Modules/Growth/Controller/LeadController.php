<?php

declare(strict_types=1);

namespace App\Modules\Growth\Controller;

use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Growth\Repository\LeadRepository;

final class LeadController extends BaseController
{
    public function __construct(private readonly LeadRepository $leads) {}

    public function index(Request $request): Response
    {
        return $this->view('admin.leads.index', ['leads' => $this->leads->allForAdmin($request->input('status')), 'counts' => $this->leads->counts(), 'currentStatus' => $request->input('status')]);
    }

    public function show(Request $request): Response
    {
        $lead = $this->leads->findWithService((int)$request->param('id'));
        if (!$lead) return Response::text('Not found', 404);
        return $this->view('admin.leads.show', ['lead' => $lead]);
    }

    public function update(Request $request): Response
    {
        $status = (string)$request->input('status', 'new');
        $allowed = ['new','contacted','qualified','quote_sent','won','lost','spam'];
        if (!in_array($status, $allowed, true)) $status = 'new';
        $this->leads->update((int)$request->param('id'), ['status' => $status, 'notes' => trim((string)$request->input('notes', '')) ?: null]);
        Session::flash('_success', 'Lead updated.');
        return $this->redirect(Config::get('admin.path', '/admin') . '/leads/' . (int)$request->param('id'));
    }

}
