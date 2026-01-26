<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Entity extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'vat',
        'email',
        'phone',
        'address',
        'status',
    ];

    // Relationships
    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function calendarEvents(): MorphMany
    {
        return $this->morphMany(CalendarEvent::class, 'eventable');
    }
}
