<?php

declare(strict_types=1);

namespace App\Modules\Assistance\Controller;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Assistance\Repository\AssistanceNotificationRepository;
use App\Modules\Assistance\Repository\AssistanceQuoteRepository;
use App\Modules\Assistance\Repository\AssistanceRequestRepository;
use App\Modules\Assistance\Repository\AssistanceWorkRepository;
use App\Modules\Assistance\Service\AssistanceNotificationService;
use App\Modules\Assistance\Service\AssistanceQuoteService;
use App\Modules\Assistance\Service\AssistanceReceiptService;
use App\Modules\Assistance\Service\AssistanceRequestService;
use App\Modules\Assistance\Service\AssistanceWorkService;
use App\Modules\Cms\Repository\ServiceRepository;

final class AssistanceController extends BaseController
{
    public function __construct(
        private readonly AssistanceRequestService $service,
        private readonly AssistanceRequestRepository $requests,
        private readonly ServiceRepository $services,
        private readonly AssistanceQuoteService $quotes,
        private readonly AssistanceQuoteRepository $quoteRepository,
        private readonly AssistanceWorkService $work,
        private readonly AssistanceWorkRepository $workRepository,
        private readonly AssistanceReceiptService $receipts,
        private readonly AssistanceNotificationRepository $notificationRepository,
        private readonly AssistanceNotificationService $notificationService
    ) {}

    public function create(Request $request): Response
    {
        return $this->view('public.assistance.create', [
            'services' => $this->services->allPublished(),
            'errors' => Session::getFlash('_errors') ?? [],
            'old' => array_merge(['category' => (string) $request->input('category', ''), 'service_id' => (string) $request->input('service_id', '')], Session::getFlash('_old') ?? []),
        ]);
    }

    public function store(Request $request): Response
    {
        $result = $this->service->submit($request->all(), $request->ip(), $request->userAgent());
        if (!$result['success']) {
            Session::flash('_errors', $result['errors']);
            Session::flash('_old', $request->only(['name', 'phone', 'email', 'category', 'service_id', 'intake_answers', 'message', 'preferred_contact', 'consent']));
            return $this->back();
        }
        return $this->redirect('/get-help/thanks?ref=' . rawurlencode($result['reference']));
    }

    public function thanks(Request $request): Response
    {
        return $this->view('public.assistance.thanks', ['reference' => trim((string) $request->input('ref'))]);
    }

    public function index(Request $request): Response
    {
        return $this->view('admin.assistance.index', ['requests' => $this->requests->allForAdmin($request->input('status')), 'counts' => $this->requests->counts(), 'currentStatus' => $request->input('status')]);
    }

    public function show(Request $request): Response
    {
        $item = $this->requests->findWithDetails((int) $request->param('id'));
        if (!$item) return Response::text('Not found', 404);
        return $this->view('admin.assistance.show', ['requestItem' => $item]);
    }

    public function quoteCreate(Request $request): Response
    {
        $item = $this->requests->findWithDetails((int) $request->param('id'));
        if (!$item) return Response::text('Not found', 404);
        return $this->view('admin.assistance.quote-create', ['requestItem' => $item]);
    }

    public function quoteStore(Request $request): Response
    {
        $requestId = (int) $request->param('id');
        if (!$this->requests->findWithDetails($requestId)) return Response::text('Not found', 404);
        $descriptions = (array) $request->input('description', []);
        $quantities = (array) $request->input('quantity', []);
        $prices = (array) $request->input('unit_price', []);
        $items = [];
        $max = max(count($descriptions), count($quantities), count($prices));
        for ($i = 0; $i < $max; $i++) {
            $items[] = ['description' => $descriptions[$i] ?? '', 'quantity' => $quantities[$i] ?? 1, 'unit_price' => $prices[$i] ?? 0];
        }
        $expires = trim((string) $request->input('expires_at', ''));
        $result = $this->quotes->create($requestId, (int) Auth::id(), $items, trim((string) $request->input('note', '')), $expires !== '' ? $expires : null);
        if (!$result['success']) {
            Session::flash('_errors', ['quote' => [$result['message']]]);
            return $this->back();
        }
        Session::flash('_success', 'Quote created and marked as sent.');
        return $this->redirect(Config::get('admin.path', '/admin') . '/assistance/quote/' . $result['id']);
    }

    public function quoteShow(Request $request): Response
    {
        $quote = $this->quoteRepository->findAdmin((int) $request->param('id'));
        if (!$quote) return Response::text('Not found', 404);
        return $this->view('admin.assistance.quote-show', [
            'quote' => $quote,
            'items' => $this->quoteRepository->items((int) $quote['id']),
            'events' => $this->quoteRepository->events((int) $quote['id']),
            'payments' => $this->quoteRepository->payments((int) $quote['id']),
            'publicUrl' => rtrim(Config::get('app.url', ''), '/') . '/quote/' . rawurlencode((string) ($this->quotes->decryptToken((string) $quote['public_token_encrypted']) ?? '')),
        ]);
    }

    public function quotePublic(Request $request): Response
    {
        $token = trim((string) $request->param('token'));
        if (!preg_match('/^[a-f0-9]{48}$/', $token)) return Response::text('Quote not found', 404, ['Cache-Control' => 'no-store']);
        $quote = $this->quoteRepository->findPublicByToken($token);
        if (!$quote) return Response::text('Quote not found', 404, ['Cache-Control' => 'no-store']);
        if (!empty($quote['expires_at']) && strtotime((string) $quote['expires_at']) < time() && !in_array($quote['status'], ['expired', 'paid', 'cancelled'], true)) {
            $this->quoteRepository->updateQuote((int) $quote['id'], ['status' => 'expired']);
            $quote['status'] = 'expired';
            $this->quoteRepository->addEvent((int) $quote['id'], 'expired', 'system', null, 'Quote expired after its expiry time.');
        }
        return $this->view('public.assistance.quote', ['quote' => $quote, 'items' => $this->quoteRepository->items((int) $quote['id']), 'payments' => $this->quoteRepository->payments((int) $quote['id']), 'token' => $token]);
    }

    public function quoteAccept(Request $request): Response
    {
        $token = trim((string) $request->param('token'));
        $result = $this->quotes->accept($token, $request->all());
        Session::flash($result['success'] ? '_success' : '_errors', $result['success'] ? 'Quote accepted. You can now submit your payment reference.' : ['quote' => [$result['message']]]);
        return $this->redirect('/quote/' . rawurlencode($token));
    }

    public function quotePayment(Request $request): Response
    {
        $token = trim((string) $request->param('token'));
        $result = $this->quotes->submitPayment($token, $request->all());
        Session::flash($result['success'] ? '_success' : '_errors', $result['success'] ? 'Payment submitted for verification. Keep your M-Pesa message until we confirm it.' : ['payment' => [$result['message']]]);
        return $this->redirect('/quote/' . rawurlencode($token));
    }

    public function payments(Request $request): Response
    {
        return $this->view('admin.assistance.payments', ['payments' => $this->quoteRepository->pendingPayments()]);
    }

    public function paymentVerify(Request $request): Response
    {
        $result = $this->quotes->verifyPayment((int) $request->param('id'), (int) Auth::id());
        Session::flash($result['success'] ? '_success' : '_errors', $result['success'] ? 'Payment verified and quote marked paid.' : ['payment' => [$result['message'] ?? 'Unable to verify payment.']]);
        return $this->redirect(Config::get('admin.path', '/admin') . '/assistance/payments');
    }

    public function paymentReject(Request $request): Response
    {
        $result = $this->quotes->rejectPayment((int) $request->param('id'), (int) Auth::id(), trim((string) $request->input('reason', '')) ?: null);
        Session::flash($result['success'] ? '_success' : '_errors', $result['success'] ? 'Payment rejected.' : ['payment' => [$result['message'] ?? 'Unable to reject payment.']]);
        return $this->redirect(Config::get('admin.path', '/admin') . '/assistance/payments');
    }

    public function work(Request $request): Response
    {
        $item = $this->requests->findWithDetails((int) $request->param('id'));
        if (!$item) return Response::text('Not found', 404);
        $review = $this->workRepository->reviewByRequest((int) $item['id']);
        return $this->view('admin.assistance.work', ['requestItem' => $item, 'tasks' => $this->workRepository->tasks((int) $item['id']), 'updates' => $this->workRepository->updates((int) $item['id']), 'staff' => $this->workRepository->activeStaff(), 'review' => $review, 'reviewUrl' => $review ? $this->work->reviewPublicUrl((int) $item['id'], $item) : null, 'portalUrl' => rtrim(Config::get('app.url', ''), '/') . '/request/' . rawurlencode($this->work->portalTokenForRequest($item))]);
    }

    public function assign(Request $request): Response
    {
        $id = (int) $request->param('id');
        $result = $this->work->assign($id, $request->input('assigned_to') !== '' ? (int) $request->input('assigned_to') : null, trim((string) $request->input('due_at', '')) ?: null, (int) Auth::id());
        Session::flash($result['success'] ? '_success' : '_errors', $result['success'] ? 'Work assignment updated.' : ['work' => [$result['message']]]);
        return $this->redirect(Config::get('admin.path', '/admin') . '/assistance/' . $id . '/work');
    }

    public function taskStore(Request $request): Response
    {
        $id = (int) $request->param('id');
        $result = $this->work->addTask($id, (string) $request->input('title', ''), $request->input('description'), (string) $request->input('priority', 'normal'), $request->input('assigned_to') !== '' ? (int) $request->input('assigned_to') : null, trim((string) $request->input('due_at', '')) ?: null, (int) Auth::id());
        Session::flash($result['success'] ? '_success' : '_errors', $result['success'] ? 'Task added.' : ['work' => [$result['message']]]);
        return $this->redirect(Config::get('admin.path', '/admin') . '/assistance/' . $id . '/work');
    }

    public function taskUpdate(Request $request): Response
    {
        $taskId = (int) $request->param('taskId');
        $task = $this->workRepository->task($taskId);
        if (!$task) return Response::text('Not found', 404);
        $result = $this->work->updateTask($taskId, (string) $request->input('status', 'pending'), $request->input('assigned_to') !== '' ? (int) $request->input('assigned_to') : null, trim((string) $request->input('due_at', '')) ?: null, (int) Auth::id());
        Session::flash($result['success'] ? '_success' : '_errors', $result['success'] ? 'Task updated.' : ['work' => [$result['message']]]);
        return $this->redirect(Config::get('admin.path', '/admin') . '/assistance/' . (int) $task['assistance_request_id'] . '/work');
    }

    public function updateStore(Request $request): Response
    {
        $id = (int) $request->param('id');
        $result = $this->work->addUpdate($id, (int) Auth::id(), (string) $request->input('message', ''), (string) $request->input('visibility', 'customer'), (string) $request->input('update_type', 'progress'));
        Session::flash($result['success'] ? '_success' : '_errors', $result['success'] ? 'Customer update added.' : ['work' => [$result['message']]]);
        return $this->redirect(Config::get('admin.path', '/admin') . '/assistance/' . $id . '/work');
    }

    public function complete(Request $request): Response
    {
        $id = (int) $request->param('id');
        $result = $this->work->complete($id, (string) $request->input('completion_note', ''), (int) Auth::id());
        Session::flash($result['success'] ? '_success' : '_errors', $result['success'] ? 'Work marked completed. You can now send the customer the review link.' : ['work' => [$result['message']]]);
        return $this->redirect(Config::get('admin.path', '/admin') . '/assistance/' . $id . '/work');
    }

    public function portal(Request $request): Response
    {
        $token = trim((string) $request->param('token'));
        if (!preg_match('/^[a-f0-9]{48}$/', $token)) return Response::text('Request not found', 404, ['Cache-Control' => 'no-store']);
        $stmt = $this->db()->prepare('SELECT ar.*, s.name AS service_name FROM assistance_requests ar LEFT JOIN services s ON s.id = ar.service_id WHERE ar.customer_token_hash = :hash LIMIT 1');
        $stmt->execute(['hash' => hash('sha256', $token)]);
        $item = $stmt->fetch();
        if (!$item) return Response::text('Request not found', 404, ['Cache-Control' => 'no-store']);
        $payment = $this->quoteRepository->latestVerifiedPaymentForRequest((int) $item['id']);
        $receiptUrl = $payment && !empty($payment['receipt_token_encrypted']) ? rtrim(Config::get('app.url', ''), '/') . '/receipt/' . rawurlencode($this->quotes->decryptToken((string) $payment['receipt_token_encrypted']) ?? '') : null;
        $review = $this->workRepository->reviewByRequest((int) $item['id']);
        return $this->view('public.assistance.portal', ['requestItem' => $item, 'tasks' => $this->workRepository->tasks((int) $item['id']), 'updates' => $this->workRepository->updates((int) $item['id'], true), 'token' => $token, 'review' => $review, 'reviewToken' => $review ? $this->work->decryptToken((string) $review['public_token_encrypted']) : null, 'receiptUrl' => $receiptUrl]);
    }

    public function notificationPreferences(Request $request): Response
    {
        $token = trim((string) $request->param('token')); $item = $this->requestByPortalToken($token);
        if (!$item) return Response::text('Request not found', 404);
        return $this->view('public.assistance.notification-preferences', ['requestItem' => $item, 'token' => $token, 'preferences' => $this->notificationRepository->preference((int) $item['id']), 'errors' => Session::getFlash('_errors') ?? [], 'success' => Session::getFlash('_success')]);
    }

    public function updateNotificationPreferences(Request $request): Response
    {
        $token = trim((string) $request->param('token')); $item = $this->requestByPortalToken($token);
        if (!$item) return Response::text('Request not found', 404);
        $ok = $this->notificationRepository->savePreference((int) $item['id'], !empty($item['customer_user_id']) ? (int) $item['customer_user_id'] : null, ['email_enabled' => $request->input('email_notifications') === '1', 'sms_enabled' => $request->input('sms_notifications') === '1', 'whatsapp_enabled' => $request->input('whatsapp_notifications') === '1']);
        Session::flash($ok ? '_success' : '_errors', $ok ? 'Notification preferences updated.' : ['notifications' => ['We could not save your preferences.']]);
        return $this->redirect('/request/' . rawurlencode($token) . '/notifications');
    }

    private function requestByPortalToken(string $token): ?array
    {
        if ($token === '' || !preg_match('/^[a-f0-9]{48}$/', $token)) return null;
        $stmt = $this->db()->prepare('SELECT ar.*, s.name AS service_name FROM assistance_requests ar LEFT JOIN services s ON s.id = ar.service_id WHERE ar.customer_token_hash = :hash LIMIT 1');
        $stmt->execute(['hash' => hash('sha256', $token)]);
        return $stmt->fetch() ?: null;
    }

    public function review(Request $request): Response
    {
        $token = trim((string) $request->param('token')); $review = $this->workRepository->reviewByToken($token);
        if (!$review) return Response::text('Review not found', 404, ['Cache-Control' => 'no-store']);
        return $this->view('public.assistance.review', ['review' => $review, 'token' => $token, 'errors' => Session::getFlash('_errors') ?? [], 'success' => Session::getFlash('_success')]);
    }

    public function reviewStore(Request $request): Response
    {
        $token = trim((string) $request->param('token')); $result = $this->work->submitReview($token, (int) $request->input('rating', 0), (string) $request->input('comment', ''));
        Session::flash($result['success'] ? '_success' : '_errors', $result['success'] ? 'Thank you. Your review has been submitted for moderation.' : ['review' => [$result['message']]]);
        return $this->redirect('/review/' . rawurlencode($token));
    }

    public function reviews(Request $request): Response { return $this->view('admin.assistance.reviews', ['reviews' => $this->workRepository->pendingReviews()]); }

    public function reviewModerate(Request $request): Response
    {
        $id = (int) $request->param('id'); $result = $this->work->moderateReview($id, (string) $request->input('status', 'rejected'), (string) $request->input('note', ''), (int) Auth::id());
        Session::flash($result['success'] ? '_success' : '_errors', $result['success'] ? 'Review moderation saved.' : ['review' => [$result['message']]]);
        return $this->redirect(Config::get('admin.path', '/admin') . '/assistance/reviews');
    }

    public function notifications(Request $request): Response { return $this->view('admin.assistance.notifications', ['notifications' => $this->notificationRepository->allForAdmin()]); }

    public function notificationRetry(Request $request): Response
    {
        $result = $this->notificationService->retry((int) $request->param('id'));
        Session::flash($result['success'] ? '_success' : '_errors', $result['success'] ? 'Notification queued for retry.' : ['notification' => [$result['message']]]);
        return $this->redirect(Config::get('admin.path', '/admin') . '/assistance/notifications');
    }

    public function receipt(Request $request): Response
    {
        $token = trim((string) $request->param('token'));
        if (!preg_match('/^[a-f0-9]{48}$/', $token)) return Response::text('Receipt not found', 404, ['Cache-Control' => 'no-store']);
        $payment = $this->quoteRepository->findPaymentByReceiptToken($token);
        if (!$payment || ($payment['status'] ?? null) !== 'verified') return Response::text('Receipt not found', 404, ['Cache-Control' => 'no-store']);
        $pdf = $this->receipts->renderForToken($token);
        if ($pdf === null) return Response::text('Receipt not found', 404, ['Cache-Control' => 'no-store']);
        return Response::binary($pdf, 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="albatech-receipt.pdf"', 'Cache-Control' => 'private, no-store']);
    }

    private function db(): \PDO { return \App\Core\Database::connection(); }
}
