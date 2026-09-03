<?php

declare(strict_types=1);

namespace App\Modules\Orders\Controller;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Orders\Repository\OrderDocumentRepository;
use App\Modules\Orders\Repository\OrderRepository;
use App\Modules\Orders\Service\OrderService;
use App\Modules\Payments\Integration\OrderPaymentHandler;
use App\Modules\Payments\Repository\PaymentRepository;

final class AdminOrderController extends BaseController
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly OrderRepository $orders,
        private readonly OrderDocumentRepository $documents,
        private readonly PaymentRepository $payments,
        private readonly OrderPaymentHandler $orderPaymentHandler
    ) {
    }

    public function index(Request $request): Response
    {
        $status = (string) $request->input('status', '');

        return $this->view('admin.orders.index', [
            'orders' => $this->orders->allForAdmin($status ?: null),
            'currentStatus' => $status,
        ]);
    }

    public function show(Request $request): Response
    {
        $orderId = (int) $request->param('id');
        $order = $this->orders->findWithDetails($orderId);

        if (!$order) {
            return Response::text('Not found', 404);
        }

        return $this->view('admin.orders.show', [
            'order' => $order,
            'history' => $this->orders->statusHistory($orderId),
            'documents' => $this->documents->forOrder($orderId),
        ]);
    }

    public function updateStatus(Request $request): Response
    {
        $orderId = (int) $request->param('id');
        $status = (string) $request->input('status', '');
        $note = trim((string) $request->input('note', '')) ?: null;

        $result = $this->orderService->transition($orderId, $status, (int) Auth::id(), $note);

        Session::flash($result['success'] ? '_success' : '_errors', $result['success']
            ? 'Order status updated.'
            : ['status' => [$result['message']]]);

        return $this->redirect(Config::get('admin.path', '/admin') . '/orders/' . $orderId);
    }

    public function quote(Request $request): Response
    {
        $orderId = (int) $request->param('id');
        $price = (float) $request->input('quoted_price', 0);
        $note = trim((string) $request->input('note', '')) ?: null;

        if ($price <= 0) {
            Session::flash('_errors', ['quoted_price' => ['Enter a valid price.']]);

            return $this->back();
        }

        $result = $this->orderService->setQuote($orderId, $price, (int) Auth::id(), $note);

        Session::flash($result['success'] ? '_success' : '_errors', $result['success']
            ? 'Quote sent to customer.'
            : ['quoted_price' => [$result['message']]]);

        return $this->redirect(Config::get('admin.path', '/admin') . '/orders/' . $orderId);
    }

    public function markPaid(Request $request): Response
    {
        $orderId = (int) $request->param('id');
        $result = $this->orderService->markPaid($orderId, (int) Auth::id());

        Session::flash($result['success'] ? '_success' : '_errors', $result['success']
            ? 'Order marked as paid.'
            : ['status' => [$result['message']]]);

        return $this->redirect(Config::get('admin.path', '/admin') . '/orders/' . $orderId);
    }

    public function refund(Request $request): Response
    {
        $orderId = (int) $request->param('id');
        $reason = trim((string) $request->input('reason', ''));
        $processViaGateway = (bool) $request->input('process_via_gateway', false);

        if (!Auth::can('payments.refund')) {
            return Response::text('Forbidden', 403);
        }

        if ($reason === '') {
            Session::flash('_errors', ['refund' => ['Please give a reason for the refund.']]);
            return $this->redirect(Config::get('admin.path', '/admin') . '/orders/' . $orderId);
        }

        $payment = $this->payments->completedForContext('order', $orderId);

        if (!$payment) {
            Session::flash('_errors', ['refund' => ['No completed payment found for this order.']]);
            return $this->redirect(Config::get('admin.path', '/admin') . '/orders/' . $orderId);
        }

        $result = $this->orderPaymentHandler->refund((int) $payment['id'], (int) Auth::id(), $reason, $processViaGateway);

        Session::flash($result['success'] ? '_success' : '_errors', $result['success']
            ? 'Order cancelled and refund recorded.'
            : ['refund' => [$result['message'] ?? 'Refund failed.']]);

        return $this->redirect(Config::get('admin.path', '/admin') . '/orders/' . $orderId);
    }
}
