<?php

namespace App\Services\Meta;

use App\Models\MetaAccount;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Crypt;

class MetaAuthService
{
    private Client $client;
    private string $appId;
    private string $appSecret;
    private string $redirectUri;
    private string $apiVersion;

    public function __construct()
    {
        $this->client      = new Client(['timeout' => 30]);
        $this->appId       = config('services.meta.app_id');
        $this->appSecret   = config('services.meta.app_secret');
        $this->redirectUri = config('services.meta.redirect_uri');
        $this->apiVersion  = config('services.meta.api_version');
    }

    public function getAuthUrl(): string
    {
        $params = http_build_query([
            'client_id'     => $this->appId,
            'redirect_uri'  => $this->redirectUri,
            'scope'         => 'ads_management,ads_read,business_management,read_insights',
            'response_type' => 'code',
            'state'         => csrf_token(),
        ]);

        return "https://www.facebook.com/{$this->apiVersion}/dialog/oauth?{$params}";
    }

    public function exchangeCodeForToken(string $code): string
    {
        $response = $this->client->get(
            "https://graph.facebook.com/{$this->apiVersion}/oauth/access_token",
            [
                'query' => [
                    'client_id'     => $this->appId,
                    'client_secret' => $this->appSecret,
                    'redirect_uri'  => $this->redirectUri,
                    'code'          => $code,
                ],
            ]
        );

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['access_token'];
    }

    public function getLongLivedToken(string $shortToken): string
    {
        $response = $this->client->get(
            "https://graph.facebook.com/{$this->apiVersion}/oauth/access_token",
            [
                'query' => [
                    'grant_type'        => 'fb_exchange_token',
                    'client_id'         => $this->appId,
                    'client_secret'     => $this->appSecret,
                    'fb_exchange_token' => $shortToken,
                ],
            ]
        );

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['access_token'];
    }

    public function getMetaUserInfo(string $token): array
    {
        $response = $this->client->get(
            "https://graph.facebook.com/{$this->apiVersion}/me",
            [
                'query' => [
                    'fields'       => 'id,name,email',
                    'access_token' => $token,
                ],
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getAdAccounts(string $token, string $metaUserId): array
    {
        $response = $this->client->get(
            "https://graph.facebook.com/{$this->apiVersion}/{$metaUserId}/adaccounts",
            [
                'query' => [
                    'fields'       => 'id,name,currency,timezone_name,account_status',
                    'access_token' => $token,
                ],
            ]
        );

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['data'] ?? [];
    }

    public function saveMetaAccount(User $user, string $code): MetaAccount
    {
        $shortToken    = $this->exchangeCodeForToken($code);
        $longToken     = $this->getLongLivedToken($shortToken);
        $metaUser      = $this->getMetaUserInfo($longToken);
        $adAccounts    = $this->getAdAccounts($longToken, $metaUser['id']);

        $metaAccount = MetaAccount::updateOrCreate(
            [
                'user_id'      => $user->id,
                'meta_user_id' => $metaUser['id'],
            ],
            [
                'tenant_id'        => $user->tenant_id,
                'access_token'     => $shortToken,
                'long_lived_token' => $longToken,
                'token_expires_at' => now()->addDays(60),
                'status'           => 'active',
                'account_name'     => $metaUser['name'] ?? null,
                'ad_accounts'      => $adAccounts,
                'permissions'      => ['ads_management', 'ads_read', 'business_management'],
            ]
        );

        return $metaAccount;
    }

    public function verifyToken(string $token): bool
    {
        try {
            $response = $this->client->get(
                "https://graph.facebook.com/debug_token",
                [
                    'query' => [
                        'input_token'  => $token,
                        'access_token' => "{$this->appId}|{$this->appSecret}",
                    ],
                ]
            );

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['data']['is_valid'] ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
