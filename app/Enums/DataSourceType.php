<?php

namespace App\Enums;

enum DataSourceType: string
{
    case CSV = 'csv';
    case JSON = 'json';
    case API = 'api';
    case DATABASE = 'database';
    case EXCEL = 'excel';
    case XML = 'xml';
    case GOOGLE_SHEETS = 'google_sheets';
    case WEBHOOK = 'webhook';

    public function getLabel(): string
    {
        return match ($this) {
            self::CSV => 'CSV File',
            self::JSON => 'JSON File',
            self::API => 'REST API',
            self::DATABASE => 'Database Query',
            self::EXCEL => 'Excel File',
            self::XML => 'XML File',
            self::GOOGLE_SHEETS => 'Google Sheets',
            self::WEBHOOK => 'Webhook',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::CSV => '📊',
            self::JSON => '📋',
            self::API => '🌐',
            self::DATABASE => '🗃️',
            self::EXCEL => '📈',
            self::XML => '📄',
            self::GOOGLE_SHEETS => '📑',
            self::WEBHOOK => '🔗',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::CSV => 'Upload CSV files with comma-separated values',
            self::JSON => 'Upload JSON files with structured data',
            self::API => 'Connect to external REST API endpoints',
            self::DATABASE => 'Query data directly from database tables',
            self::EXCEL => 'Upload Excel files (.xlsx, .xls)',
            self::XML => 'Upload XML files with structured data',
            self::GOOGLE_SHEETS => 'Connect to Google Sheets documents',
            self::WEBHOOK => 'Receive data from webhook endpoints',
        };
    }

    public function getSupportedExtensions(): array
    {
        return match ($this) {
            self::CSV => ['csv', 'txt'],
            self::JSON => ['json'],
            self::EXCEL => ['xlsx', 'xls'],
            self::XML => ['xml'],
            default => []
        };
    }

    public function getSupportedMimeTypes(): array
    {
        return match ($this) {
            self::CSV => [
                'text/csv',
                'text/plain',
                'application/csv',
                'text/comma-separated-values'
            ],
            self::JSON => [
                'application/json',
                'text/json'
            ],
            self::EXCEL => [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel',
                'application/excel'
            ],
            // TODO:
            // self::XML => [
            //     'application/xml',
            //     'text/xml'
            // ],
            default => []
        };
    }

    public function getDefaultConfig(): array
    {
        return match ($this) {

            self::CSV => [
                'delimiter' => ',',
                'enclosure' => '"',
                'escape' => '\\',
                'has_header' => true,
                'encoding' => 'UTF-8',
                'skip_empty_lines' => true
            ],
            self::JSON => [
                'root_path' => null,
                'flatten_nested' => false,
                'encoding' => 'UTF-8'
            ],
            self::API => [
                'method' => 'GET',
                'headers' => [],
                'authentication' => 'none',
                'timeout' => 30,
                'retry_attempts' => 3,
                'cache_duration' => 300,
                'data_path' => null
            ],
            self::DATABASE => [
                'connection' => 'mysql',
                'query_type' => 'table',
                'table_name' => null,
                'custom_query' => null,
                'cache_duration' => 300
            ],
            self::EXCEL => [
                'worksheet' => 0,
                'has_header' => true,
                'start_row' => 1,
                'end_row' => null
            ],
            self::XML => [
                'root_element' => null,
                'record_element' => null,
                'namespace' => null
            ],
            self::GOOGLE_SHEETS => [
                'spreadsheet_id' => null,
                'sheet_name' => 'Sheet1',
                'range' => 'A:Z',
                'has_header' => true,
                'credentials_type' => 'service_account'
            ],
            self::WEBHOOK => [
                'secret_token' => null,
                'allowed_ips' => [],
                'data_format' => 'json',
                'storage_method' => 'database'
            ]
        };
    }

    public function supportsFileUpload(): bool
    {
        return in_array($this, [
            self::CSV,
            self::JSON,
            self::EXCEL,
            self::XML
        ]);
    }

    public function requiresExternalConnection(): bool
    {
        return in_array($this, [
            self::API,
            self::DATABASE,
            self::GOOGLE_SHEETS,
            self::WEBHOOK
        ]);
    }

    public function supportsRealTimeUpdates(): bool
    {
        return in_array($this, [
            self::API,
            self::DATABASE,
            self::WEBHOOK
        ]);
    }

    public function requiresAuthentication(): bool
    {
        return in_array($this, [
            self::API,
            self::GOOGLE_SHEETS,
            self::WEBHOOK
        ]);
    }

    public function getValidationRules(): array
    {
        return match ($this) {
            self::CSV => [
                'file' => 'required|file|mimes:csv,txt|max:10240',
                'delimiter' => 'string|in:,;|\\t',
                'has_header' => 'boolean'
            ],
            self::JSON => [
                'file' => 'required|file|mimes:json|max:10240',
                'root_path' => 'nullable|string'
            ],
            self::API => [
                'url' => 'required|url',
                'method' => 'required|in:GET,POST',
                'headers' => 'array',
                'timeout' => 'integer|min:1|max:300'
            ],
            self::DATABASE => [
                'connection' => 'required|string',
                'table_name' => 'required_if:query_type,table|string',
                'custom_query' => 'required_if:query_type,query|string'
            ],
            self::EXCEL => [
                'file' => 'required|file|mimes:xlsx,xls|max:20480',
                'worksheet' => 'integer|min:0',
                'has_header' => 'boolean'
            ],
            self::XML => [
                'file' => 'required|file|mimes:xml|max:10240',
                'root_element' => 'required|string',
                'record_element' => 'required|string'
            ],
            self::GOOGLE_SHEETS => [
                'spreadsheet_id' => 'required|string',
                'sheet_name' => 'required|string',
                'credentials' => 'required|json'
            ],
            self::WEBHOOK => [
                'secret_token' => 'nullable|string|min:16',
                'allowed_ips' => 'array',
                'data_format' => 'required|in:json,xml,form'
            ]
        };
    }

    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getLabel();
        }
        return $options;
    }

    public static function getGroupedOptions(): array
    {
        return [
            'File Upload' => [
                self::CSV->value => self::CSV->getLabel(),
                self::EXCEL->value => self::EXCEL->getLabel(),
                self::JSON->value => self::JSON->getLabel(),
                self::XML->value => self::XML->getLabel(),
            ],
            'External Sources' => [
                self::API->value => self::API->getLabel(),
                self::GOOGLE_SHEETS->value => self::GOOGLE_SHEETS->getLabel(),
                self::WEBHOOK->value => self::WEBHOOK->getLabel(),
            ],
            'Database' => [
                self::DATABASE->value => self::DATABASE->getLabel(),
            ]
        ];
    }

    public static function getFileUploadTypes(): array
    {
        return collect(self::cases())
            ->filter(fn($case) => $case->supportsFileUpload())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }

    public static function getExternalConnectionTypes(): array
    {
        return collect(self::cases())
            ->filter(fn($case) => $case->requiresExternalConnection())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
