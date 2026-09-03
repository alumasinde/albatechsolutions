<?php

declare(strict_types=1);

namespace App\Modules\Cms\Controller;

use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Cms\Repository\FaqRepository;
use App\Modules\Cms\Service\FaqService;

final class FaqController extends BaseController
{
    public function __construct(
        private readonly FaqService $faqService,
        private readonly FaqRepository $faqs
    ) {
    }

    public function index(Request $request): Response
    {
        return $this->view('admin.faqs.index', ['faqs' => $this->faqs->allForAdmin()]);
    }

    public function store(Request $request): Response
    {
        if (trim((string) $request->input('question', '')) === '') {
            Session::flash('_errors', ['question' => ['Question is required.']]);

            return $this->back();
        }

        $this->faqService->create($request->all());
        Session::flash('_success', 'FAQ added.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/faqs');
    }

    public function update(Request $request): Response
    {
        $this->faqService->update((int) $request->param('id'), $request->all());
        Session::flash('_success', 'FAQ updated.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/faqs');
    }

    public function destroy(Request $request): Response
    {
        $this->faqService->delete((int) $request->param('id'));
        Session::flash('_success', 'FAQ deleted.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/faqs');
    }
}
