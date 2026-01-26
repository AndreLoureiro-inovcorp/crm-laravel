<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmission extends Model
{
    protected $fillable = [
        'public_form_id',
        'data',
        'ip_address',
        'submitted_at',
    ];

    protected $casts = [
        'data' => 'array',
        'submitted_at' => 'datetime',
    ];

    // Relationships
    public function publicForm(): BelongsTo
    {
        return $this->belongsTo(PublicForm::class);
    }
}
