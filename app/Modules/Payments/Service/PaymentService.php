<?php

declare(strict_types=1);

namespace App\Modules\Payments\Service;

use App\Core\AuditLog;
use App\Core\Config;
use App\Core\Logger;
use App\Modules\Payments\Gateway\PaymentGatewayInterface;
use App\Modules\Payments\Repository\PaymentRepository;

/**
 * Provider-agnostic payment application service.
 *
 * It deliberately knows nothing about orders, invoices, carts or bookings.
 * A consuming system supplies context_type/context_id and decides what
 * business action should happen after a payment is completed.
 */
final class PaymentService
{
    private const ALLOWED_PROOF_MIME = ['image/jpeg', 'image/png', 'application/pdf'];
    private const MAX_PROOF_BYTES = 5 * 1024 * 1024;
    private const REUSABLE_WINDOW_SECONDS = 900; // 15 minutes — conservative default; check your Paystack dashboard's Session Timeout setting and adjust to match

    public function __construct(
        private readonly PaymentRepository $payments,
        private readonly PaymentGatewayInterface $gateway
    ) {
    }

    /**
     * @param array{
     *   context_type:string,
     *   context_id:int,
     *   amount:float|int,
     *   currency?:string,
     *   customer_email:string,
     *   customer_phone?:string|null,
     *   description?:string|null,
     *   metadata?:array<string,mixed>
     * } $input
     */
    public function initializeOnline(array $input): array
    {
        $contextType = trim((string) ($input['context_type'] ?? ''));
        $contextId = (int) ($input['context_id'] ?? 0);
        $email = trim((string) ($input['customer_email'] ?? ''));
        $amount = (float) ($input['amount'] ?? 0);

        if ($contextType === '' || $contextId <= 0 || $email === '' || $amount <= 0) {
            return ['success' => false, 'message' => 'Invalid payment details.'];
        }

        $currency = strtoupper((string) ($input['currency'] ?? Config::get('paystack.currency', 'KES')));
        $latest = $this->payments->latestForContext($contextType, $contextId);

        if ($latest && $latest['status'] === 'pending') {
            $ageSeconds = time() - strtotime((string) $latest['created_at']);

            if ($ageSeconds < self::REUSABLE_WINDOW_SECONDS) {
                $meta = json_decode((string) ($latest['metadata'] ?? ''), true) ?: [];

                // Same reference, same access_code, same Paystack transaction — resuming
                // it (rather than starting a new one) is what actually makes double
                // charges impossible: there is only ever one live transaction per order
                // at a time, and it's also near-instant since no API call is needed.
                if (!empty($meta['access_code'])) {
                    return [
                        'success' => true,
                        'payment_id' => (int) $latest['id'],
                        'reference' => $latest['reference'],
                        'authorization_url' => $meta['authorization_url'] ?? null,
                        'access_code' => $meta['access_code'],
                        'resumed' => true,
                    ];
                }

                return ['success' => false, 'message' => 'A payment for this item is already in progress. Please wait a moment and try again.'];
            }

            // Stale/abandoned attempt (e.g. customer closed the popup without completing
            // or cancelling it) — expire it so a new attempt isn't blocked indefinitely.
            $this->payments->update((int) $latest['id'], [
                'status' => 'failed',
                'gateway_response' => 'Expired: superseded by a new payment attempt.',
            ]);
        }

        $reference = $this->generateReference($contextType, $contextId);
        $amountSubunit = $this->toSubunit($amount, $currency);
        $baseMetadata = $input['metadata'] ?? [];

        $paymentId = $this->payments->create([
            'context_type' => $contextType,
            'context_id' => $contextId,
            'gateway' => 'paystack',
            'method' => 'online',
            'channel' => null,
            'status' => 'pending',
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => $currency,
            'reference' => $reference,
            'gateway_transaction_id' => null,
            'gateway_response' => null,
            'customer_email' => $email,
            'customer_phone' => $input['customer_phone'] ?? null,
            'metadata' => !empty($baseMetadata) ? json_encode($baseMetadata, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
        ]);

        $result = $this->gateway->initialize([
            'reference' => $reference,
            'amount' => $amountSubunit,
            'currency' => $currency,
            'email' => $email,
            'phone' => $input['customer_phone'] ?? null,
            'description' => $input['description'] ?? null,
            'metadata' => array_merge($input['metadata'] ?? [], [
                'context_type' => $contextType,
                'context_id' => $contextId,
                'payment_id' => $paymentId,
            ]),
            'callback_url' => Config::get('paystack.callback_url'),
        ]);

        if (!$result['success']) {
            $this->payments->update($paymentId, [
                'status' => 'failed',
                'gateway_response' => $result['message'] ?? 'Initialization failed.',
            ]);

            return ['success' => false, 'message' => $result['message'] ?? 'Could not initialize payment.'];
        }

        $this->payments->update($paymentId, [
            'metadata' => json_encode(array_merge($baseMetadata, [
                'access_code' => $result['access_code'] ?? null,
                'authorization_url' => $result['authorization_url'] ?? null,
            ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]);

        AuditLog::record('payment.initialized', 'payment', $paymentId, [
            'gateway' => 'paystack',
            'reference' => $reference,
            'context_type' => $contextType,
            'context_id' => $contextId,
        ]);

        return [
            'success' => true,
            'payment_id' => $paymentId,
            'reference' => $reference,
            'authorization_url' => $result['authorization_url'] ?? null,
            'access_code' => $result['access_code'] ?? null,
        ];
    }

    /**
     * Verifies a transaction directly with the provider. The caller must
     * still decide what business value should be delivered.
     */
    public function markFulfilled(int $paymentId): void
    {
        $this->payments->update($paymentId, [
            'fulfilled_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function verifyReference(string $reference): array
    {
        $payment = $this->payments->findByReference($reference);

        if (!$payment) {
            return ['success' => false, 'message' => 'Payment reference not found.'];
        }

        $result = $this->gateway->verify($reference);

        if (!$result['success']) {
            return $result;
        }

        $data = $result['data'] ?? [];

        if (($data['status'] ?? '') === 'success') {
            $this->completeFromGatewayData($payment, $data);
        } elseif (in_array(($data['status'] ?? ''), ['failed', 'abandoned', 'reversed'], true)) {
            $this->failFromGatewayData($payment, $data);
        }

        return ['success' => true, 'payment' => $this->payments->find((int) $payment['id'])];
    }

    /**
     * Verify a Paystack webhook signature and process charge.success.
     * The returned payment can then be passed to the host application's
     * fulfillment/mark-paid adapter.
     */
    public function handlePaystackWebhook(string $rawBody, string $signature): array
    {
        $secret = trim((string) Config::get('paystack.secret_key', ''));

        if ($secret === '' || $signature === '') {
            return ['success' => false, 'status' => 401, 'message' => 'Webhook authentication failed.'];
        }

        $computed = hash_hmac('sha512', $rawBody, $secret);
        if (!hash_equals($computed, $signature)) {
            Logger::security('Paystack webhook signature mismatch.');
            return ['success' => false, 'status' => 401, 'message' => 'Invalid webhook signature.'];
        }

        $event = json_decode($rawBody, true);
        if (!is_array($event)) {
            return ['success' => false, 'status' => 400, 'message' => 'Invalid webhook payload.'];
        }

        if (($event['event'] ?? '') !== 'charge.success') {
            return ['success' => true, 'ignored' => true];
        }

        $data = $event['data'] ?? [];
        $reference = trim((string) ($data['reference'] ?? ''));
        if ($reference === '') {
            return ['success' => false, 'status' => 400, 'message' => 'Webhook payment reference is missing.'];
        }

        $payment = $this->payments->findByReference($reference);
        if (!$payment) {
            Logger::warning('Paystack webhook references unknown payment.', ['reference' => $reference]);
            return ['success' => true, 'ignored' => true];
        }

        if ($payment['status'] === 'completed') {
            return ['success' => true, 'payment' => $payment, 'duplicate' => true];
        }

        $expectedAmount = $this->toSubunit((float) $payment['amount'], (string) $payment['currency']);
        $receivedAmount = (int) ($data['amount'] ?? -1);
        $receivedCurrency = strtoupper((string) ($data['currency'] ?? ''));

        if ($expectedAmount !== $receivedAmount || strtoupper((string) $payment['currency']) !== $receivedCurrency) {
            Logger::security('Paystack webhook amount/currency mismatch.', [
                'payment_id' => $payment['id'],
                'reference' => $reference,
                'expected_amount' => $expectedAmount,
                'received_amount' => $receivedAmount,
                'expected_currency' => $payment['currency'],
                'received_currency' => $receivedCurrency,
            ]);

            $this->payments->update((int) $payment['id'], [
                'status' => 'failed',
                'gateway_response' => 'Webhook amount or currency mismatch.',
            ]);

            return ['success' => false, 'status' => 400, 'message' => 'Payment details did not match.'];
        }

        $this->completeFromGatewayData($payment, $data);

        return ['success' => true, 'payment' => $this->payments->find((int) $payment['id'])];
    }

    /**
     * @return array{success:bool,message?:string,payment_id?:int}
     */
    public function submitManualProof(array $context, array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Please attach a proof of payment file.'];
        }

        if (($file['size'] ?? 0) > self::MAX_PROOF_BYTES) {
            return ['success' => false, 'message' => 'File exceeds the 5MB limit.'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, self::ALLOWED_PROOF_MIME, true)) {
            return ['success' => false, 'message' => 'Only PDF, JPG, and PNG files are accepted.'];
        }

        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
            default => 'bin',
        };

        $contextType = trim((string) ($context['context_type'] ?? ''));
        $contextId = (int) ($context['context_id'] ?? 0);
        if ($contextType === '' || $contextId <= 0) {
            return ['success' => false, 'message' => 'Invalid payment context.'];
        }

        $uploadDir = dirname(__DIR__, 4) . '/storage/uploads/payments/' . $contextType . '/' . $contextId;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destination = $uploadDir . '/' . bin2hex(random_bytes(16)) . '.' . $extension;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'message' => 'Could not save the uploaded file.'];
        }

        $paymentId = $this->payments->create([
            'context_type' => $contextType,
            'context_id' => $contextId,
            'gateway' => 'manual',
            'method' => 'bank_transfer',
            'channel' => 'bank',
            'status' => 'pending',
            'amount' => number_format((float) $context['amount'], 2, '.', ''),
            'currency' => strtoupper((string) ($context['currency'] ?? 'KES')),
            'reference' => $this->generateReference('manual', $contextId),
            'proof_path' => $destination,
            'customer_email' => $context['customer_email'] ?? null,
            'customer_phone' => $context['customer_phone'] ?? null,
        ]);

        AuditLog::record('payment.manual_submitted', 'payment', $paymentId);

        return ['success' => true, 'payment_id' => $paymentId];
    }

    public function refund(int $paymentId, int $staffId, string $reason, bool $processViaGateway): array
    {
        $payment = $this->payments->find($paymentId);

        if (!$payment || $payment['status'] !== 'completed') {
            return ['success' => false, 'message' => 'Only a completed payment can be refunded.'];
        }

        if ($processViaGateway) {
            if (($payment['gateway'] ?? '') !== 'paystack') {
                return ['success' => false, 'message' => 'Automatic refund is only available for Paystack payments. Refund it manually, then use "already refunded" to record it here.'];
            }

            $result = $this->gateway->refund((string) $payment['reference'], null, $reason ?: null);

            if (!$result['success']) {
                return ['success' => false, 'message' => $result['message'] ?? 'Paystack refund request failed.'];
            }
        }

        $this->payments->update($paymentId, [
            'status' => 'refunded',
            'refunded_at' => date('Y-m-d H:i:s'),
            'refunded_by' => $staffId,
            'refund_reason' => $reason ?: null,
        ]);

        AuditLog::record('payment.refunded', 'payment', $paymentId, [
            'processed_via_gateway' => $processViaGateway,
            'reason' => $reason,
        ]);

        return ['success' => true, 'payment' => $this->payments->find($paymentId)];
    }

    public function verifyManual(int $paymentId, int $staffId): array
    {
        $payment = $this->payments->find($paymentId);

        if (!$payment || $payment['gateway'] !== 'manual' || $payment['status'] !== 'pending') {
            return ['success' => false, 'message' => 'This payment cannot be verified.'];
        }

        $this->payments->update($paymentId, [
            'status' => 'completed',
            'verified_by' => $staffId,
            'verified_at' => date('Y-m-d H:i:s'),
            'gateway_response' => 'Verified manually by staff.',
        ]);

        AuditLog::record('payment.manual_verified', 'payment', $paymentId);

        return ['success' => true, 'payment' => $this->payments->find($paymentId)];
    }

    public function rejectManual(int $paymentId, int $staffId, ?string $reason): array
    {
        $payment = $this->payments->find($paymentId);

        if (!$payment || $payment['gateway'] !== 'manual' || $payment['status'] !== 'pending') {
            return ['success' => false, 'message' => 'This payment cannot be rejected.'];
        }

        $this->payments->update($paymentId, [
            'status' => 'rejected',
            'verified_by' => $staffId,
            'verified_at' => date('Y-m-d H:i:s'),
            'gateway_response' => $reason,
        ]);

        AuditLog::record('payment.manual_rejected', 'payment', $paymentId);

        return ['success' => true];
    }

    private function completeFromGatewayData(array $payment, array $data): void
    {
        if ($payment['status'] === 'completed') {
            return;
        }

        $this->payments->update((int) $payment['id'], [
            'status' => 'completed',
            'gateway_transaction_id' => isset($data['id']) ? (string) $data['id'] : null,
            'channel' => $data['channel'] ?? null,
            'gateway_response' => $data['gateway_response'] ?? $data['message'] ?? 'Successful',
            'customer_phone' => $data['customer']['phone'] ?? $payment['customer_phone'] ?? null,
        ]);

        AuditLog::record('payment.completed', 'payment', (int) $payment['id'], [
            'gateway' => $payment['gateway'],
            'reference' => $payment['reference'],
        ]);
    }

    private function failFromGatewayData(array $payment, array $data): void
    {
        $this->payments->update((int) $payment['id'], [
            'status' => 'failed',
            'gateway_transaction_id' => isset($data['id']) ? (string) $data['id'] : null,
            'channel' => $data['channel'] ?? null,
            'gateway_response' => $data['gateway_response'] ?? $data['message'] ?? 'Payment failed.',
        ]);

        AuditLog::record('payment.failed', 'payment', (int) $payment['id'], [
            'gateway' => $payment['gateway'],
            'reference' => $payment['reference'],
        ]);
    }

    private function toSubunit(float $amount, string $currency): int
    {
        // KES uses cents/subunits for Paystack's API.
        return (int) round($amount * 100);
    }

    private function generateReference(string $contextType, int $contextId): string
    {
        return strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $contextType) ?: 'PAY', 0, 8))
            . '-' . $contextId . '-' . strtoupper(bin2hex(random_bytes(5)));
    }
}
