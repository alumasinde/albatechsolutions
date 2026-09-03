<?php

declare(strict_types=1);

namespace App\Modules\Payments\Gateway;

use App\Core\Config;
use App\Core\Logger;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

final class PaystackGateway implements PaymentGatewayInterface
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => rtrim((string) Config::get('paystack.base_url', 'https://api.paystack.co'), '/') . '/',
            'timeout' => 12,
            'connect_timeout' => 5,
            'http_errors' => false,
        ]);
    }

    public function initialize(array $payment): array
    {
        $secret = trim((string) Config::get('paystack.secret_key', ''));

        if ($secret === '') {
            Logger::error('Paystack is not configured: secret key is missing.');
            return ['success' => false, 'message' => 'Online payments are not configured yet.'];
        }

        $payload = [
            'amount' => (string) $payment['amount'],
            'email' => $payment['email'],
            'currency' => strtoupper($payment['currency']),
            'reference' => $payment['reference'],
        ];

        if (!empty($payment['phone'])) {
            $payload['metadata'] = ['customer_phone' => $payment['phone']];
        }

        if (!empty($payment['metadata']) && is_array($payment['metadata'])) {
            $payload['metadata'] = array_merge($payload['metadata'] ?? [], $payment['metadata']);
        }

        if (!empty($payment['description'])) {
            $payload['metadata']['description'] = $payment['description'];
        }

        $callbackUrl = $payment['callback_url'] ?? Config::get('paystack.callback_url');
        if (!empty($callbackUrl)) {
            $payload['callback_url'] = $callbackUrl;
        }

        try {
            $response = $this->client->post('transaction/initialize', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $secret,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $data = json_decode((string) $response->getBody(), true);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300 && ($data['status'] ?? false)) {
                return [
                    'success' => true,
                    'authorization_url' => $data['data']['authorization_url'] ?? null,
                    'access_code' => $data['data']['access_code'] ?? null,
                    'reference' => $data['data']['reference'] ?? $payment['reference'],
                ];
            }

            Logger::warning('Paystack: transaction initialization failed', ['response' => $data]);

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Paystack could not initialize this payment.',
            ];
        } catch (GuzzleException $e) {
            Logger::warning('Paystack: initialization request failed: ' . $e->getMessage());

            return ['success' => false, 'message' => 'Could not reach the payment provider right now. Please try again shortly.'];
        }
    }

    public function verify(string $reference): array
    {
        $secret = trim((string) Config::get('paystack.secret_key', ''));

        if ($secret === '') {
            return ['success' => false, 'message' => 'Online payments are not configured yet.'];
        }

        try {
            $response = $this->client->get('transaction/verify/' . rawurlencode($reference), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $secret,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300 && ($data['status'] ?? false)) {
                return ['success' => true, 'data' => $data['data'] ?? []];
            }

            Logger::warning('Paystack: transaction verification failed', ['reference' => $reference, 'response' => $data]);

            return ['success' => false, 'message' => $data['message'] ?? 'Could not verify the payment.'];
        } catch (GuzzleException $e) {
            Logger::warning('Paystack: verification request failed: ' . $e->getMessage());

            return ['success' => false, 'message' => 'Could not verify the payment right now.'];
        }
    }

    public function refund(string $reference, ?int $amountSubunit = null, ?string $reason = null): array
    {
        $secret = trim((string) Config::get('paystack.secret_key', ''));

        if ($secret === '') {
            Logger::error('Paystack is not configured: secret key is missing.');
            return ['success' => false, 'message' => 'Online payments are not configured yet.'];
        }

        $payload = ['transaction' => $reference];

        if ($amountSubunit !== null) {
            $payload['amount'] = $amountSubunit;
        }

        if ($reason !== null && $reason !== '') {
            $payload['merchant_note'] = $reason;
        }

        try {
            $response = $this->client->post('refund', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $secret,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $data = json_decode((string) $response->getBody(), true);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300 && ($data['status'] ?? false)) {
                return ['success' => true, 'data' => $data['data'] ?? []];
            }

            Logger::warning('Paystack: refund request failed', ['reference' => $reference, 'response' => $data]);

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Paystack could not process this refund.',
            ];
        } catch (GuzzleException $e) {
            Logger::warning('Paystack: refund request failed: ' . $e->getMessage());

            return ['success' => false, 'message' => 'Could not reach the payment provider right now. Please try again shortly.'];
        }
    }
}
