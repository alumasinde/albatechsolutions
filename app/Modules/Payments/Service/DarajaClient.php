<?php

declare(strict_types=1);

namespace App\Modules\Payments\Service;

use App\Core\Config;
use App\Core\Logger;
use GuzzleHttp\Client;

final class DarajaClient
{
    private function baseUrl(): string
    {
        return Config::get('mpesa.env') === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    /**
     * @return string|null Access token, or null on failure.
     */
    public function getAccessToken(): ?string
    {
        $client = new Client();

        try {
            $response = $client->get($this->baseUrl() . '/oauth/v1/generate?grant_type=client_credentials', [
                'auth' => [Config::get('mpesa.consumer_key'), Config::get('mpesa.consumer_secret')],
            ]);

            $data = json_decode((string) $response->getBody(), true);

            return $data['access_token'] ?? null;
        } catch (\Throwable $e) {
            Logger::warning('Daraja: failed to get access token: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * @return array{success: bool, checkout_request_id?: string, merchant_request_id?: string, message?: string}
     */
    public function stkPush(string $phone, float $amount, string $accountReference, string $description): array
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return ['success' => false, 'message' => 'Could not connect to M-Pesa right now. Please try again shortly.'];
        }

        $shortcode = Config::get('mpesa.shortcode');
        $passkey = Config::get('mpesa.passkey');
        $timestamp = date('YmdHis');
        $password = base64_encode($shortcode . $passkey . $timestamp);
        $normalizedPhone = $this->normalizePhone($phone);

        $client = new Client();

        try {
            $response = $client->post($this->baseUrl() . '/mpesa/stkpush/v1/processrequest', [
                'headers' => ['Authorization' => 'Bearer ' . $token],
                'json' => [
                    'BusinessShortCode' => $shortcode,
                    'Password'          => $password,
                    'Timestamp'         => $timestamp,
                    'TransactionType'   => 'CustomerPayBillOnline',
                    'Amount'            => (int) round($amount),
                    'PartyA'            => $normalizedPhone,
                    'PartyB'            => $shortcode,
                    'PhoneNumber'       => $normalizedPhone,
                    'CallBackURL'       => Config::get('mpesa.callback_url'),
                    'AccountReference'  => $accountReference,
                    'TransactionDesc'   => $description,
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);

            if (($data['ResponseCode'] ?? null) === '0') {
                return [
                    'success' => true,
                    'checkout_request_id' => $data['CheckoutRequestID'],
                    'merchant_request_id' => $data['MerchantRequestID'],
                ];
            }

            Logger::warning('Daraja: STK push rejected', ['response' => $data]);

            return ['success' => false, 'message' => $data['ResponseDescription'] ?? 'M-Pesa declined the request.'];
        } catch (\Throwable $e) {
            Logger::warning('Daraja: STK push request failed: ' . $e->getMessage());

            return ['success' => false, 'message' => 'Could not reach M-Pesa right now. Please try again shortly.'];
        }
    }

    /**
     * Daraja expects 2547XXXXXXXX / 2541XXXXXXXX — normalize common
     * Kenyan formats (07..., +2547..., 2547...) into that shape.
     */
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '0')) {
            $digits = '254' . substr($digits, 1);
        } elseif (str_starts_with($digits, '7') || str_starts_with($digits, '1')) {
            $digits = '254' . $digits;
        }

        return $digits;
    }
}
