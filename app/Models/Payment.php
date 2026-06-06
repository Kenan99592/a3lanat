<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'user_id',
        'tenant_id',
        'amount',
        'currency',
        'method',
        'status',
        'reference',
        'notes',
        'paid_at',
        'meta_data',
    ];

    protected $casts = [
        'amount'    => 'decimal:2',
        'paid_at'   => 'datetime',
        'meta_data' => 'array',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
