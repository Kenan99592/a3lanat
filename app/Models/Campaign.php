<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'tenant_id',
        'meta_account_id',
        'name',
        'objective',
        'status',
        'budget_type',
        'budget',
        'start_date',
        'end_date',
        'meta_campaign_id',
        'meta_data',
    ];

    protected $casts = [
        'meta_data'  => 'array',
        'start_date' => 'date',
        'end_date'   => 'date',
        'budget'     => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function metaAccount(): BelongsTo
    {
        return $this->belongsTo(MetaAccount::class);
    }

    public function adSets(): HasMany
    {
        return $this->hasMany(AdSet::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'ACTIVE';
    }
}
