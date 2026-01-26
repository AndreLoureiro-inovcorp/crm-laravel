<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'trigger_type',
        'trigger_value',
        'action_type',
        'is_active',
    ];

    protected $casts = [
        'trigger_value' => 'integer',
        'is_active' => 'boolean',
    ];
}
