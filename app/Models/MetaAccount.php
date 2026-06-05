<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class MetaAccount extends Model
{
    protected $fillable = [
        'user_id',
        'tenant_id',
        'meta_user_id',
        'meta_ad_account_id',
        'meta_business_id',
        'access_token',
        'long_lived_token',
        'token_expires_at',
        'status',
        'permissions',
    ];

    protected $casts = [
        'permissions'      => 'array',
        'token_expires_at' => 'datetime',
    ];

    protected $hidden = [
        'access_token',
        'long_lived_token',
    ];

    public function setAccessTokenAttribute(string $value): void
    {
        $this->attributes['access_token'] = Crypt::encryptString($value);
    }

    public function getAccessTokenAttribute(string $value): string
    {
        return Crypt::decryptString($value);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }
}
