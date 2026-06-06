<?php

namespace App\Services\Meta;

use App\Models\MetaAccount;
use App\Models\User;
use GuzzleHttp\Client;

class MetaAccountService
{
    private Client $client;
    private string $apiVersion;

    public function __construct()
    {
        $this->client     = new Client(['timeout' => 30]);
        $this->apiVersion = config('services.meta.api_version');
    }

    public function getAccounts(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return MetaAccount::where('user_id', $user->id)
                          ->where('tenant_id', $user->tenant_id)
                          ->get()
                          ->map(function ($account) {
                              return [
                                  'id'           => $account->id,
                                  'account_name' => $account->account_name,
                                  'meta_user_id' => $account->meta_user_id,
                                  'status'       => $account->status,
                                  'ad_accounts'  => $account->ad_accounts,
                                  'expires_at'   => $account->token_expires_at,
                                  'is_expired'   => $account->isExpired(),
                              ];
                          });
    }

    public function disconnect(User $user, int $accountId): bool
    {
        $account = MetaAccount::where('id', $accountId)
                              ->where('user_id', $user->id)
                              ->where('tenant_id', $user->tenant_id)
                              ->first();

        if (!$account) {
            return false;
        }

        $account->update(['status' => 'revoked']);
        return true;
    }

    public function refreshAdAccounts(User $user, int $accountId): array
    {
        $account = MetaAccount::where('id', $accountId)
                              ->where('user_id', $user->id)
                              ->first();

        if (!$account) {
            return [];
        }

        $metaAuth   = new MetaAuthService();
        $adAccounts = $metaAuth->getAdAccounts(
            $account->long_lived_token ?? $account->access_token,
            $account->meta_user_id
        );

        $account->update(['ad_accounts' => $adAccounts]);
        return $adAccounts;
    }
}
