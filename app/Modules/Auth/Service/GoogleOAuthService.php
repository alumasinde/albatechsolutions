<?php

declare(strict_types=1);

namespace App\Modules\Auth\Service;

use App\Core\Config;
use App\Core\Logger;
use GuzzleHttp\Client;

final class GoogleOAuthService
{
    private const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const USERINFO_URL = 'https://www.googleapis.com/oauth2/v3/userinfo';

    public function authorizationUrl(string $state): string
    {
        $params = [
            'client_id'     => Config::get('google.client_id'),
            'redirect_uri'  => Config::get('google.redirect_uri'),
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $state,
            'access_type'   => 'online',
            'prompt'        => 'select_account',
        ];

        return self::AUTH_URL . '?' . http_build_query($params);
    }

    /**
     * @return array{email: string, name: string, google_id: string}|null
     */
    public function handleCallback(string $code): ?array
    {
        $accessToken = $this->exchangeCodeForToken($code);

        if (!$accessToken) {
            return null;
        }

        return $this->fetchProfile($accessToken);
    }

    private function exchangeCodeForToken(string $code): ?string
    {
        $client = new Client();

        try {
            $response = $client->post(self::TOKEN_URL, [
                'form_params' => [
                    'code'          => $code,
                    'client_id'     => Config::get('google.client_id'),
                    'client_secret' => Config::get('google.client_secret'),
                    'redirect_uri'  => Config::get('google.redirect_uri'),
                    'grant_type'    => 'authorization_code',
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);

            return $data['access_token'] ?? null;
        } catch (\Throwable $e) {
            Logger::warning('Google OAuth token exchange failed: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * @return array{email: string, name: string, google_id: string}|null
     */
    private function fetchProfile(string $accessToken): ?array
    {
        $client = new Client();

        try {
            $response = $client->get(self::USERINFO_URL, [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            ]);

            $data = json_decode((string) $response->getBody(), true);

            if (empty($data['email']) || empty($data['email_verified'])) {
                Logger::warning('Google OAuth: email missing or unverified', ['data' => $data]);

                return null;
            }

            return [
                'email'     => $data['email'],
                'name'      => $data['name'] ?? $data['email'],
                'google_id' => $data['sub'],
            ];
        } catch (\Throwable $e) {
            Logger::warning('Google OAuth profile fetch failed: ' . $e->getMessage());

            return null;
        }
    }
}
