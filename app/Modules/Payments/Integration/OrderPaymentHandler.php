<?php

declare(strict_types=1);

namespace App\Modules\Payments\Integration;

use App\Modules\Orders\Service\OrderService;
use App\Modules\Payments\Service\PaymentService;

/**
 * AlbaTech's adapter between the generic Payments module and Orders.
 * Other systems can replace this adapter with an invoice/cart/subscription
 * fulfillment handler without changing the Payments module itself.
 */
final class OrderPaymentHandler
{
    public function __construct(
        private readonly OrderService $orders,
        private readonly PaymentService $payments
    ) {
    }

    public function complete(array $payment, ?int $actorId = null): array
    {
        if (($payment['context_type'] ?? '') !== 'order') {
            return ['success' => false, 'message' => 'Payment context is not an order.'];
        }

        $result = $this->orders->markPaid(
            (int) $payment['context_id'],
            $actorId,
            sprintf('Payment completed via %s (reference: %s).', strtoupper((string) $payment['gateway']), $payment['reference'])
        );

        if ($result['success']) {
            $this->payments->markFulfilled((int) $payment['id']);
        }

        return $result;
    }

    /**
     * Refunds a completed payment and cancels the order it paid for.
     * $processViaGateway triggers a live Paystack refund call; pass false
     * when the refund was already handled manually (e.g. a bank transfer,
     * or a Paystack refund issued directly from their dashboard) and this
     * should only update the records.
     */
    public function refund(int $paymentId, int $staffId, string $reason, bool $processViaGateway): array
    {
        $refundResult = $this->payments->refund($paymentId, $staffId, $reason, $processViaGateway);

        if (!$refundResult['success']) {
            return $refundResult;
        }

        $payment = $refundResult['payment'];

        if (($payment['context_type'] ?? '') !== 'order') {
            return $refundResult;
        }

        return $this->orders->transition(
            (int) $payment['context_id'],
            'cancelled',
            $staffId,
            'Order cancelled — payment refunded. ' . $reason
        );
    }
}
