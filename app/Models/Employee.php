<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'guid',
        'first_name',
        'last_name',
        'email',
        'manager_id',
        'synced_at',
        'store_id'
    ];

    protected $casts = [
        'first_name' => 'string',
        'last_name' => 'string',
        'guid' => 'string',
        'store_id' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}