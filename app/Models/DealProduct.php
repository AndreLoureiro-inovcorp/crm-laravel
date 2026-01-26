<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealProduct extends Model
{
    protected $fillable = [
        'deal_id',
        'product_name',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // Relationships
    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }
}
