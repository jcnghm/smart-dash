<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dashboard extends Model
{
    protected $fillable = [
        'user_id',
        'uuid',
        'name',
        'description',
        'is_public',
        'grid_config'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'grid_config' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function widgets(): HasMany
    {
        return $this->hasMany(Widget::class)->orderBy('position_y')->orderBy('position_x');
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
