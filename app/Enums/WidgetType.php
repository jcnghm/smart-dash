<?php

namespace App\Enums;

enum WidgetType: string
{
    case COUNTER = 'counter';
    case CHART_LINE = 'chart_line';
    case CHART_BAR = 'chart_bar';
    case CHART_PIE = 'chart_pie';
    case CHART_AREA = 'chart_area';
    case CHART_SCATTER = 'chart_scatter';
    case TABLE = 'table';
    case TEXT = 'text';
    case GAUGE = 'gauge';
    case PROGRESS = 'progress';

    public function getLabel(): string
    {
        return match ($this) {
            self::COUNTER => 'Counter',
            self::CHART_LINE => 'Line Chart',
            self::CHART_BAR => 'Bar Chart',
            self::CHART_PIE => 'Pie Chart',
            self::CHART_AREA => 'Area Chart',
            self::CHART_SCATTER => 'Scatter Plot',
            self::TABLE => 'Data Table',
            self::TEXT => 'Text Widget',
            self::GAUGE => 'Gauge',
            self::PROGRESS => 'Progress Bar',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::COUNTER => '📊',
            self::CHART_LINE => '📈',
            self::CHART_BAR => '📊',
            self::CHART_PIE => '🥧',
            self::CHART_AREA => '🏔️',
            self::CHART_SCATTER => '🎯',
            self::TABLE => '📋',
            self::TEXT => '📝',
            self::GAUGE => '⚡',
            self::PROGRESS => '📶',
        };
    }

    public function getDefaultConfig(): array
    {
        return match ($this) {
            self::COUNTER => [
                'prefix' => '',
                'suffix' => '',
                'color' => 'blue',
                'format' => 'number'
            ],
            self::CHART_LINE => [
                'x_column' => null,
                'y_columns' => [],
                'chart_color' => '#3B82F6',
                'show_points' => true,
                'smooth' => true
            ],
            self::CHART_BAR => [
                'x_column' => null,
                'y_columns' => [],
                'chart_color' => '#10B981',
                'horizontal' => false
            ],
            self::CHART_PIE => [
                'group_by' => null,
                'value_column' => null,
                'colors' => ['#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899']
            ],
            self::CHART_AREA => [
                'x_column' => null,
                'y_columns' => [],
                'chart_color' => '#8B5CF6',
                'fill_opacity' => 0.3
            ],
            self::CHART_SCATTER => [
                'x_column' => null,
                'y_column' => null,
                'chart_color' => '#F59E0B',
                'point_size' => 4
            ],
            self::TABLE => [
                'columns' => [],
                'sortable' => true,
                'pagination' => true,
                'per_page' => 10
            ],
            self::TEXT => [
                'content' => 'Sample text',
                'alignment' => 'left',
                'size' => 'medium'
            ],
            self::GAUGE => [
                'min_value' => 0,
                'max_value' => 100,
                'target_value' => 75,
                'color_ranges' => [
                    ['min' => 0, 'max' => 30, 'color' => '#EF4444'],
                    ['min' => 30, 'max' => 70, 'color' => '#F59E0B'],
                    ['min' => 70, 'max' => 100, 'color' => '#10B981']
                ]
            ],
            self::PROGRESS => [
                'target_value' => 100,
                'color' => 'blue',
                'show_percentage' => true
            ]
        };
    }

    public function getDefaultDimensions(): array
    {
        return match ($this) {
            self::COUNTER => ['width' => 3, 'height' => 2],
            self::CHART_LINE => ['width' => 8, 'height' => 4],
            self::CHART_BAR => ['width' => 6, 'height' => 4],
            self::CHART_PIE => ['width' => 4, 'height' => 4],
            self::CHART_AREA => ['width' => 8, 'height' => 4],
            self::CHART_SCATTER => ['width' => 6, 'height' => 4],
            self::TABLE => ['width' => 12, 'height' => 6],
            self::TEXT => ['width' => 6, 'height' => 2],
            self::GAUGE => ['width' => 4, 'height' => 3],
            self::PROGRESS => ['width' => 6, 'height' => 2],
        };
    }

    public function isChart(): bool
    {
        return str_starts_with($this->value, 'chart_');
    }

    public function getChartType(): ?string
    {
        if (!$this->isChart()) {
            return null;
        }

        return str_replace('chart_', '', $this->value);
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
            'Basic' => [
                self::COUNTER->value => self::COUNTER->getLabel(),
                self::TEXT->value => self::TEXT->getLabel(),
                self::PROGRESS->value => self::PROGRESS->getLabel(),
                self::GAUGE->value => self::GAUGE->getLabel(),
            ],
            'Charts' => [
                self::CHART_LINE->value => self::CHART_LINE->getLabel(),
                self::CHART_BAR->value => self::CHART_BAR->getLabel(),
                self::CHART_PIE->value => self::CHART_PIE->getLabel(),
                self::CHART_AREA->value => self::CHART_AREA->getLabel(),
                self::CHART_SCATTER->value => self::CHART_SCATTER->getLabel(),
            ],
            'Data' => [
                self::TABLE->value => self::TABLE->getLabel(),
            ]
        ];
    }
}
