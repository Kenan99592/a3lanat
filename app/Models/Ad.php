<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ad extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ad_set_id',
        'tenant_id',
        'name',
        'status',
        'format',
        'headline',
        'body',
        'link_url',
        'image_url',
        'video_url',
        'call_to_action',
        'meta_ad_id',
        'meta_data',
    ];

    protected $casts = [
        'meta_data' => 'array',
    ];

    public function adSet(): BelongsTo
    {
        return $this->belongsTo(AdSet::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
