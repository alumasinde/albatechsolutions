<?php

declare(strict_types=1);

namespace App\Modules\Admin\Controller;

use App\Core\BaseController;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Admin\Repository\AuditLogRepository;

final class AuditController extends BaseController
{
    public function __construct(
        private readonly AuditLogRepository $auditLogs
    ) {
    }

    public function index(Request $request): Response
    {
        $page = max(1, (int) $request->input('page', 1));

        return $this->view('admin.audit.index', [
            'logs' => $this->auditLogs->paginate($page),
            'page' => $page,
            'total' => $this->auditLogs->count(),
        ]);
    }
}
