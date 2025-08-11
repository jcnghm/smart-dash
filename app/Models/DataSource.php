<?php
// app/Models/DataSource.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\DataSourceType;
use Illuminate\Support\Facades\Storage;

class DataSource extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'file_path',
        'url',
        'columns',
        'row_count',
        'config',
        'last_synced_at',
        'is_active'
    ];

    protected $casts = [
        'type' => DataSourceType::class,
        'columns' => 'array',
        'config' => 'array',
        'last_synced_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function widgets(): HasMany
    {
        return $this->hasMany(Widget::class);
    }

    public function getTypeEnum(): DataSourceType
    {
        return $this->type;
    }

    public function supportsFileUpload(): bool
    {
        return $this->type->supportsFileUpload();
    }

    public function requiresExternalConnection(): bool
    {
        return $this->type->requiresExternalConnection();
    }

    public function supportsRealTimeUpdates(): bool
    {
        return $this->type->supportsRealTimeUpdates();
    }

    public function getData($limit = null)
    {
        return match ($this->type) {
            DataSourceType::CSV => $this->getCsvData($limit),
            DataSourceType::JSON => $this->getJsonData($limit),
            DataSourceType::EXCEL => $this->getExcelData($limit),
            DataSourceType::API => $this->getApiData($limit),
            DataSourceType::DATABASE => $this->getDatabaseData($limit),
            DataSourceType::XML => $this->getXmlData($limit),
            DataSourceType::GOOGLE_SHEETS => $this->getGoogleSheetsData($limit),
            DataSourceType::WEBHOOK => $this->getWebhookData($limit),
            default => collect()
        };
    }

    public function getFileSize(): ?string
    {
        if (!$this->file_path || !Storage::exists($this->file_path)) {
            return null;
        }

        $bytes = Storage::size($this->file_path);
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function needsRefresh(): bool
    {
        if (!$this->supportsRealTimeUpdates()) {
            return false;
        }

        $cacheMinutes = $this->config['cache_duration'] ?? 300;
        return $this->last_synced_at === null ||
            $this->last_synced_at->diffInSeconds(now()) > $cacheMinutes;
    }

    private function getCsvData($limit = null)
    {
        if (!$this->file_path || !Storage::exists($this->file_path)) {
            return collect();
        }

        // TODO: Implement CSV reading logic
        return collect();
    }

    private function getJsonData($limit = null)
    {
        if (!$this->file_path || !Storage::exists($this->file_path)) {
            return collect();
        }

        $content = Storage::get($this->file_path);
        $data = json_decode($content, true);

        if ($rootPath = $this->config['root_path'] ?? null) {
            $data = data_get($data, $rootPath);
        }

        $collection = collect($data);
        return $limit ? $collection->take($limit) : $collection;
    }

    private function getExcelData($limit = null)
    {
        // Implementation for reading Excel data
        return collect();
    }

    private function getApiData($limit = null)
    {
        // Implementation for fetching API data
        return collect();
    }

    private function getDatabaseData($limit = null)
    {
        // Implementation for database queries
        return collect();
    }

    private function getXmlData($limit = null)
    {
        // Implementation for reading XML data
        return collect();
    }

    private function getGoogleSheetsData($limit = null)
    {
        // Implementation for Google Sheets API
        return collect();
    }

    private function getWebhookData($limit = null)
    {
        // Implementation for webhook data
        return collect();
    }
}
