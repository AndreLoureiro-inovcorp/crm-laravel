<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatConversation extends Model
{
    protected $fillable = [
        'user_id',
        'messages',
    ];

    protected $casts = [
        'messages' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
