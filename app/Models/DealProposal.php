<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealProposal extends Model
{
    protected $fillable = [
        'deal_id',
        'file_path',
        'sent_at',
        'sent_by',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    // Relationships
    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
