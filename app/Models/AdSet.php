<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdSet extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'tenant_id',
        'name',
        'status',
        'daily_budget',
        'lifetime_budget',
        'targeting',
        'billing_event',
        'optimization_goal',
        'start_time',
        'end_time',
        'meta_ad_set_id',
        'meta_data',
    ];

    protected $casts = [
        'targeting'  => 'array',
        'meta_data'  => 'array',
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function ads(): HasMany
    {
        return $this->hasMany(Ad::class);
    }
}
