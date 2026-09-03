<?php

declare(strict_types=1);

namespace App\Modules\Payments\Controller;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Payments\Integration\OrderPaymentHandler;
use App\Modules\Payments\Repository\PaymentRepository;
use App\Modules\Payments\Service\PaymentService;
use App\Modules\Payments\Service\ReceiptService;
use App\Modules\Orders\Repository\OrderRepository;

final class AdminPaymentController extends BaseController
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly PaymentRepository $payments,
        private readonly OrderPaymentHandler $orderPaymentHandler,
        private readonly ReceiptService $receipts,
        private readonly OrderRepository $orders
    ) {
    }

    public function index(Request $request): Response
    {
        $status = (string) $request->input('status', '');

        return $this->view('admin.payments.index', [
            'pending' => $this->payments->pendingBankTransfers(),
            'payments' => $this->payments->allWithDetails($status ?: null),
            'currentStatus' => $status,
        ]);
    }

    public function receipt(Request $request): Response
    {
        $payment = $this->payments->find((int) $request->param('id'));

        if (!$payment || $payment['status'] !== 'completed' || $payment['context_type'] !== 'order') {
            return Response::text('Receipt not available for this payment.', 404);
        }

        return $this->streamReceipt($payment);
    }

    public function receiptForOrder(Request $request): Response
    {
        $orderId = (int) $request->param('id');
        $payment = $this->payments->completedForContext('order', $orderId);

        if (!$payment) {
            return Response::text('No completed payment found for this order.', 404);
        }

        return $this->streamReceipt($payment);
    }

    private function streamReceipt(array $payment): Response
    {
        $order = $this->orders->findWithDetails((int) $payment['context_id']);

        if (!$order) {
            return Response::text('Not found', 404);
        }

        $pdf = $this->receipts->render($order, $payment);

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="receipt-' . $payment['reference'] . '.pdf"');
        echo $pdf;
        exit;
    }

    public function downloadProof(Request $request): Response
    {
        $payment = $this->payments->find((int) $request->param('id'));

        if (!$payment || empty($payment['proof_path']) || !file_exists($payment['proof_path'])) {
            return Response::text('Not found', 404);
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="proof-' . $payment['id'] . '.' . pathinfo($payment['proof_path'], PATHINFO_EXTENSION) . '"');
        header('Content-Length: ' . filesize($payment['proof_path']));
        readfile($payment['proof_path']);
        exit;
    }

    public function verify(Request $request): Response
    {
        $result = $this->paymentService->verifyManual((int) $request->param('id'), (int) Auth::id());

        if ($result['success'] && !empty($result['payment'])) {
            $fulfillment = $this->orderPaymentHandler->complete($result['payment'], (int) Auth::id());
            if (!$fulfillment['success']) {
                $result = $fulfillment;
            }
        }

        Session::flash($result['success'] ? '_success' : '_errors', $result['success']
            ? 'Payment verified — order marked as paid.'
            : ['payment' => [$result['message']]]);

        return $this->redirect(Config::get('admin.path', '/admin') . '/payments');
    }

    public function reject(Request $request): Response
    {
        $reason = trim((string) $request->input('reason', '')) ?: null;
        $result = $this->paymentService->rejectManual((int) $request->param('id'), (int) Auth::id(), $reason);

        Session::flash($result['success'] ? '_success' : '_errors', $result['success']
            ? 'Payment rejected.'
            : ['payment' => [$result['message']]]);

        return $this->redirect(Config::get('admin.path', '/admin') . '/payments');
    }
}
