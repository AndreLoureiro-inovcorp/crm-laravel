<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Deal extends Model
{
    protected $fillable = [
        'tenant_id',
        'entity_id',
        'person_id',
        'title',
        'value',
        'stage',
        'probability',
        'expected_close_date',
        'owner_id',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'probability' => 'integer',
        'expected_close_date' => 'date',
    ];

    // Relationships
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(DealProduct::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(DealActivity::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(DealEmail::class);
    }

    public function proposal(): HasOne
    {
        return $this->hasOne(DealProposal::class);
    }

    public function calendarEvents(): MorphMany
    {
        return $this->morphMany(CalendarEvent::class, 'eventable');
    }
}
