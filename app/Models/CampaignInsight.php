<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignInsight extends Model
{
    protected $fillable = [
        'campaign_id',
        'tenant_id',
        'date',
        'impressions',
        'reach',
        'clicks',
        'spend',
        'cpm',
        'cpc',
        'ctr',
        'conversions',
        'cost_per_conversion',
        'meta_data',
    ];

    protected $casts = [
        'date'                => 'date',
        'meta_data'           => 'array',
        'spend'               => 'decimal:2',
        'cpm'                 => 'decimal:4',
        'cpc'                 => 'decimal:4',
        'ctr'                 => 'decimal:4',
        'cost_per_conversion' => 'decimal:4',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
