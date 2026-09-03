<?php

declare(strict_types=1);

namespace App\Modules\Payments\Controller;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Orders\Repository\OrderRepository;
use App\Modules\Payments\Integration\OrderPaymentHandler;
use App\Modules\Payments\Repository\PaymentRepository;
use App\Modules\Payments\Service\PaymentService;
use App\Modules\Payments\Service\ReceiptService;

final class PaymentController extends BaseController
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly OrderRepository $orders,
        private readonly PaymentRepository $payments,
        private readonly OrderPaymentHandler $orderPaymentHandler,
        private readonly ReceiptService $receipts
    ) {
    }

    public function show(Request $request): Response
    {
        $order = $this->getOwnedOrder($request);

        if (!$order) {
            return Response::text('Not found', 404);
        }

        if ($order['status'] !== 'accepted') {
            if (in_array($order['status'], ['in_progress', 'completed'], true)) {
                Session::flash('_success', 'This order has already been paid — no further payment is needed.');
            } else {
                Session::flash('_errors', ['payment' => ['This order is not ready for payment yet.']]);
            }
            return $this->redirect('/orders/' . $order['id']);
        }

        return $this->view('orders.pay', [
            'order' => $order,
            'latestPayment' => $this->payments->latestForContext('order', (int) $order['id']),
            'paystackPublicKey' => Config::get('paystack.public_key', ''),
        ]);
    }

    public function initiatePaystack(Request $request): Response
    {
        $order = $this->getOwnedOrder($request);

        if (!$order) {
            return Response::json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        if ($order['status'] !== 'accepted' || empty($order['quoted_price'])) {
            return Response::json(['success' => false, 'message' => 'This order is not ready for payment yet.'], 422);
        }

        $result = $this->paymentService->initializeOnline([
            'context_type' => 'order',
            'context_id' => (int) $order['id'],
            'amount' => (float) $order['quoted_price'],
            'currency' => $order['currency'] ?? 'KES',
            'customer_email' => (string) $order['customer_email'],
            'customer_phone' => $order['customer_phone'] ?? null,
            'description' => 'Payment for order ' . $order['order_number'],
            'metadata' => [
                'order_number' => $order['order_number'],
                'customer_id' => (int) $order['user_id'],
            ],
        ]);

        if (!$result['success']) {
            return Response::json(['success' => false, 'message' => $result['message']], 422);
        }

        if (empty($result['access_code'])) {
            return Response::json(['success' => false, 'message' => 'Paystack did not return a checkout session.'], 502);
        }

        return Response::json([
            'success' => true,
            'access_code' => $result['access_code'],
            'authorization_url' => $result['authorization_url'] ?? null,
            'reference' => $result['reference'] ?? null,
        ]);
    }

    public function paystackCallback(Request $request): Response
    {
        $reference = trim((string) $request->input('reference', ''));

        if ($reference === '') {
            Session::flash('_errors', ['payment' => ['No payment reference was returned.']]);
            return $this->redirect('/orders');
        }

        $result = $this->paymentService->verifyReference($reference);
        $payment = $result['payment'] ?? null;

        if (!$result['success'] || !$payment) {
            Session::flash('_errors', ['payment' => [$result['message'] ?? 'Payment verification failed.']]);
            return $this->redirect('/orders');
        }

        $status = $payment['status'] ?? '';

        if ($status === 'completed' && empty($payment['fulfilled_at'])) {
            $fulfillment = $this->orderPaymentHandler->complete($payment, null);
            if (!$fulfillment['success']) {
                Session::flash('_errors', ['payment' => [$fulfillment['message'] ?? 'Payment received, but order confirmation is pending.']]);
            } else {
                Session::flash('_success', 'Payment confirmed successfully.');
            }
        } elseif ($status === 'completed') {
            // Already fulfilled — most likely Paystack's webhook landed first, which is
            // normal and not an error. Show the same success message either way, so the
            // customer never sees a false "failed" after a payment that actually went through.
            Session::flash('_success', 'Payment confirmed successfully.');
        } else {
            Session::flash('_errors', ['payment' => ['Payment has not been completed.']]);
        }

        $contextId = (int) ($payment['context_id'] ?? 0);
        return $contextId > 0 ? $this->redirect('/orders/' . $contextId . '/pay') : $this->redirect('/orders');
    }

    public function receipt(Request $request): Response
    {
        $order = $this->getOwnedOrder($request);

        if (!$order) {
            return Response::text('Not found', 404);
        }

        $payment = $this->payments->completedForContext('order', (int) $order['id']);

        if (!$payment) {
            return Response::text('No completed payment found for this order.', 404);
        }

        $pdf = $this->receipts->render($order, $payment);

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="receipt-' . $payment['reference'] . '.pdf"');
        echo $pdf;
        exit;
    }

    public function submitBankTransfer(Request $request): Response
    {
        $order = $this->getOwnedOrder($request);

        if (!$order) {
            return Response::text('Not found', 404);
        }

        if ($order['status'] !== 'accepted' || empty($order['quoted_price'])) {
            Session::flash('_errors', ['payment' => ['This order is not ready for payment yet.']]);
            return $this->back();
        }

        $file = $request->file('proof');
        $result = $this->paymentService->submitManualProof([
            'context_type' => 'order',
            'context_id' => (int) $order['id'],
            'amount' => (float) $order['quoted_price'],
            'currency' => $order['currency'] ?? 'KES',
            'customer_email' => $order['customer_email'] ?? null,
            'customer_phone' => $order['customer_phone'] ?? null,
        ], $file ?? []);

        if (!$result['success']) {
            Session::flash('_errors', ['proof' => [$result['message']]]);
            return $this->back();
        }

        Session::flash('_success', 'Proof of payment submitted — our team will verify it shortly.');
        return $this->redirect('/orders/' . $order['id'] . '/pay');
    }

    private function getOwnedOrder(Request $request): ?array
    {
        $orderId = (int) $request->param('id');
        $order = $this->orders->findWithDetails($orderId);

        if (!$order || (int) $order['user_id'] !== (int) Auth::id()) {
            return null;
        }

        return $order;
    }
}
