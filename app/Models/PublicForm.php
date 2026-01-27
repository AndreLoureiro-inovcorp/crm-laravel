<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Scopes\TenantScope;

class PublicForm extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'fields',
        'embed_code',
        'confirmation_message',
        'is_active',
    ];

    protected $casts = [
        'fields' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    // Relationships
    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }
}
