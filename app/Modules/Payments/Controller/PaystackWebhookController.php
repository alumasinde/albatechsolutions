<?php

declare(strict_types=1);

namespace App\Modules\Payments\Controller;

use App\Core\Logger;
use App\Core\Response;
use App\Modules\Payments\Integration\OrderPaymentHandler;
use App\Modules\Payments\Service\PaymentService;

final class PaystackWebhookController
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly OrderPaymentHandler $orderPaymentHandler
    ) {
    }

    public function handle(): Response
    {
        $rawBody = file_get_contents('php://input') ?: '';
        $signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';

        $result = $this->paymentService->handlePaystackWebhook($rawBody, $signature);

        if (!$result['success']) {
            return Response::text($result['message'] ?? 'Invalid webhook.', $result['status'] ?? 400);
        }

        $payment = $result['payment'] ?? null;
        if ($payment && ($payment['status'] ?? '') === 'completed' && empty($payment['fulfilled_at'])) {
            $fulfillment = $this->orderPaymentHandler->complete($payment, null);

            if (!$fulfillment['success']) {
                Logger::warning('Paystack payment completed but order fulfillment failed.', [
                    'payment_id' => $payment['id'],
                    'context_type' => $payment['context_type'],
                    'context_id' => $payment['context_id'],
                    'message' => $fulfillment['message'] ?? 'Unknown error',
                ]);
            }
        }

        return Response::json(['received' => true]);
    }
}
