<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\WidgetType;

class Widget extends Model
{
    protected $fillable = [
        'dashboard_id',
        'type',
        'title',
        'position_x',
        'position_y',
        'width',
        'height',
        'config',
        'data_source_id',
        'refresh_interval'
    ];

    protected $casts = [
        'type' => WidgetType::class,
        'config' => 'array',
        'refresh_interval' => 'integer'
    ];

    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(Dashboard::class);
    }

    public function dataSource(): BelongsTo
    {
        return $this->belongsTo(DataSource::class);
    }

    public function getData()
    {
        if ($this->dataSource) {
            return $this->dataSource->getData();
        }

        return [];
    }

    public function getTypeEnum(): WidgetType
    {
        return $this->type;
    }

    public function isChart(): bool
    {
        return $this->type->isChart();
    }

    public function getChartType(): ?string
    {
        return $this->type->getChartType();
    }
}
