<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Dashboard;
use App\Models\Widget;
use App\Models\DataSource;
use Illuminate\Support\Facades\Storage;
use App\Enums\WidgetType;
use App\Enums\DataSourceType;
use Illuminate\Support\Str;

class DemoDashboardSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@smartdash.com'],
            [
                'name' => 'Demo User',
                'password' => bcrypt('password'),
            ]
        );

        $salesDataSource = $this->createSalesDataSource($user);
        $marketingDataSource = $this->createMarketingDataSource($user);
        $customerDataSource = $this->createCustomerDataSource($user);
        $salesDashboard = Dashboard::create([
            'user_id' => $user->id,
            'uuid' => Str::uuid(),
            'name' => 'Sales Analytics',
            'description' => 'Track sales performance and metrics',
            'is_public' => false,
            'grid_config' => ['columns' => 12, 'gap' => 4]
        ]);

        $marketingDashboard = Dashboard::create([
            'user_id' => $user->id,
            'uuid' => Str::uuid(),
            'name' => 'Marketing Overview',
            'description' => 'Monitor marketing campaigns and engagement',
            'is_public' => false,
            'grid_config' => ['columns' => 12, 'gap' => 4]
        ]);

        $this->createSalesWidgets($salesDashboard, $salesDataSource, $customerDataSource);
        $this->createMarketingWidgets($marketingDashboard, $marketingDataSource);
    }

    private function createSalesDataSource(User $user): DataSource
    {
        $salesData = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        foreach ($months as $month) {
            $salesData[] = [
                'month' => $month,
                'revenue' => rand(50000, 150000),
                'orders' => rand(100, 500),
                'customers' => rand(80, 300),
                'region' => ['North', 'South', 'East', 'West'][rand(0, 3)]
            ];
        }

        $csvContent = "month,revenue,orders,customers,region\n";
        foreach ($salesData as $row) {
            $csvContent .= implode(',', $row) . "\n";
        }
        $fileName = 'demo_sales_data.csv';
        Storage::put("demo/{$fileName}", $csvContent);

        return DataSource::create([
            'user_id' => $user->id,
            'name' => 'Sales Data 2024',
            'type' => DataSourceType::CSV,
            'file_path' => "demo/{$fileName}",
            'columns' => ['month', 'revenue', 'orders', 'customers', 'region'],
            'row_count' => count($salesData),
            'config' => [
                'has_header' => true,
                'delimiter' => ',',
                'sample_data' => array_slice($salesData, 0, 5)
            ]
        ]);
    }

    private function createMarketingDataSource(User $user): DataSource
    {
        $marketingData = [];
        $channels = ['Google Ads', 'Facebook', 'Email', 'Organic', 'Direct'];

        foreach ($channels as $channel) {
            $marketingData[] = [
                'channel' => $channel,
                'impressions' => rand(10000, 100000),
                'clicks' => rand(500, 5000),
                'conversions' => rand(20, 200),
                'cost' => rand(1000, 10000),
                'ctr' => round(rand(100, 800) / 100, 2)
            ];
        }

        $csvContent = "channel,impressions,clicks,conversions,cost,ctr\n";
        foreach ($marketingData as $row) {
            $csvContent .= implode(',', $row) . "\n";
        }

        $fileName = 'demo_marketing_data.csv';
        Storage::put("demo/{$fileName}", $csvContent);

        return DataSource::create([
            'user_id' => $user->id,
            'name' => 'Marketing Campaigns',
            'type' => 'csv',
            'file_path' => "demo/{$fileName}",
            'columns' => ['channel', 'impressions', 'clicks', 'conversions', 'cost', 'ctr'],
            'row_count' => count($marketingData),
            'config' => [
                'has_header' => true,
                'delimiter' => ',',
                'sample_data' => array_slice($marketingData, 0, 5)
            ]
        ]);
    }

    private function createCustomerDataSource(User $user): DataSource
    {
        $customerData = [];
        $names = ['John Doe', 'Jane Smith', 'Bob Johnson', 'Alice Wilson', 'Charlie Brown', 'Diana Prince', 'Frank Miller', 'Grace Lee'];
        $regions = ['North', 'South', 'East', 'West'];

        foreach ($names as $name) {
            $customerData[] = [
                'name' => $name,
                'total_sales' => rand(5000, 50000),
                'orders' => rand(5, 25),
                'region' => $regions[array_rand($regions)],
                'customer_since' => date('Y-m-d', strtotime('-' . rand(30, 365) . ' days')),
                'status' => ['Active', 'Premium', 'VIP'][rand(0, 2)]
            ];
        }

        $csvContent = "name,total_sales,orders,region,customer_since,status\n";
        foreach ($customerData as $row) {
            $csvContent .= implode(',', $row) . "\n";
        }

        $fileName = 'demo_customer_data.csv';
        Storage::put("demo/{$fileName}", $csvContent);

        return DataSource::create([
            'user_id' => $user->id,
            'name' => 'Customer Database',
            'type' => 'csv',
            'file_path' => "demo/{$fileName}",
            'columns' => ['name', 'total_sales', 'orders', 'region', 'customer_since', 'status'],
            'row_count' => count($customerData),
            'config' => [
                'has_header' => true,
                'delimiter' => ',',
                'sample_data' => array_slice($customerData, 0, 5)
            ]
        ]);
    }

    private function createSalesWidgets(Dashboard $dashboard, DataSource $salesData, DataSource $customerData)
    {
        $widgets = [
            [
                'type' => WidgetType::COUNTER,
                'title' => 'Total Revenue',
                'position_x' => 0,
                'position_y' => 0,
                'width' => 3,
                'height' => 2,
                'data_source_id' => $salesData->id,
                'config' => [
                    'metric' => 'sum',
                    'column' => 'revenue',
                    'prefix' => '$',
                    'color' => 'green',
                    'demo_value' => 1250000
                ]
            ],
            [
                'type' => WidgetType::COUNTER,
                'title' => 'Total Orders',
                'position_x' => 3,
                'position_y' => 0,
                'width' => 3,
                'height' => 2,
                'data_source_id' => $salesData->id,
                'config' => [
                    'metric' => 'sum',
                    'column' => 'orders',
                    'color' => 'blue',
                    'demo_value' => 2847
                ]
            ],
            [
                'type' => WidgetType::COUNTER,
                'title' => 'Avg Order Value',
                'position_x' => 6,
                'position_y' => 0,
                'width' => 3,
                'height' => 2,
                'data_source_id' => $salesData->id,
                'config' => [
                    'metric' => 'avg',
                    'column' => 'revenue',
                    'prefix' => '$',
                    'color' => 'purple',
                    'demo_value' => 439
                ]
            ],
            [
                'type' => WidgetType::COUNTER,
                'title' => 'New Customers',
                'position_x' => 9,
                'position_y' => 0,
                'width' => 3,
                'height' => 2,
                'data_source_id' => $customerData->id,
                'config' => [
                    'metric' => 'count',
                    'color' => 'orange',
                    'demo_value' => 156
                ]
            ],
            [
                'type' => WidgetType::CHART_LINE,
                'title' => 'Monthly Revenue Trend',
                'position_x' => 0,
                'position_y' => 2,
                'width' => 8,
                'height' => 4,
                'data_source_id' => $salesData->id,
                'config' => [
                    'x_column' => 'month',
                    'y_columns' => ['revenue'],
                    'chart_color' => '#3B82F6',
                    'show_points' => true,
                    'smooth' => true
                ]
            ],
            [
                'type' => WidgetType::CHART_PIE,
                'title' => 'Sales by Region',
                'position_x' => 8,
                'position_y' => 2,
                'width' => 4,
                'height' => 4,
                'data_source_id' => $salesData->id,
                'config' => [
                    'group_by' => 'region',
                    'value_column' => 'revenue',
                    'colors' => ['#EF4444', '#10B981', '#F59E0B', '#8B5CF6']
                ]
            ],
            [
                'type' => WidgetType::TABLE,
                'title' => 'Top Customers',
                'position_x' => 0,
                'position_y' => 6,
                'width' => 6,
                'height' => 3,
                'data_source_id' => $customerData->id,
                'config' => [
                    'columns' => ['name', 'total_sales', 'orders', 'status'],
                    'sortable' => true,
                    'sort_by' => 'total_sales',
                    'sort_direction' => 'desc',
                    'limit' => 10
                ]
            ],
            [
                'type' => WidgetType::CHART_BAR,
                'title' => 'Orders by Month',
                'position_x' => 6,
                'position_y' => 6,
                'width' => 6,
                'height' => 3,
                'data_source_id' => $salesData->id,
                'config' => [
                    'x_column' => 'month',
                    'y_columns' => ['orders'],
                    'chart_color' => '#10B981',
                    'horizontal' => false
                ]
            ]
        ];

        foreach ($widgets as $widgetData) {
            $dashboard->widgets()->create(array_merge($widgetData, [
                'refresh_interval' => 300
            ]));
        }
    }

    private function createMarketingWidgets(Dashboard $dashboard, DataSource $marketingData)
    {
        $widgets = [
            [
                'type' => WidgetType::COUNTER,
                'title' => 'Total Impressions',
                'position_x' => 0,
                'position_y' => 0,
                'width' => 3,
                'height' => 2,
                'data_source_id' => $marketingData->id,
                'config' => [
                    'metric' => 'sum',
                    'column' => 'impressions',
                    'color' => 'blue',
                    'demo_value' => 325000
                ]
            ],
            [
                'type' => WidgetType::COUNTER,
                'title' => 'Total Clicks',
                'position_x' => 3,
                'position_y' => 0,
                'width' => 3,
                'height' => 2,
                'data_source_id' => $marketingData->id,
                'config' => [
                    'metric' => 'sum',
                    'column' => 'clicks',
                    'color' => 'green',
                    'demo_value' => 12750
                ]
            ],
            [
                'type' => WidgetType::COUNTER,
                'title' => 'Avg CTR',
                'position_x' => 6,
                'position_y' => 0,
                'width' => 3,
                'height' => 2,
                'data_source_id' => $marketingData->id,
                'config' => [
                    'metric' => 'avg',
                    'column' => 'ctr',
                    'suffix' => '%',
                    'color' => 'purple',
                    'demo_value' => 3.92
                ]
            ],
            [
                'type' => WidgetType::COUNTER,
                'title' => 'Total Cost',
                'position_x' => 9,
                'position_y' => 0,
                'width' => 3,
                'height' => 2,
                'data_source_id' => $marketingData->id,
                'config' => [
                    'metric' => 'sum',
                    'column' => 'cost',
                    'prefix' => '$',
                    'color' => 'orange',
                    'demo_value' => 28500
                ]
            ],
            [
                'type' => WidgetType::CHART_LINE,
                'title' => 'Performance by Channel',
                'position_x' => 0,
                'position_y' => 2,
                'width' => 12,
                'height' => 4,
                'data_source_id' => $marketingData->id,
                'config' => [
                    'x_column' => 'channel',
                    'y_columns' => ['clicks', 'conversions'],
                    'chart_colors' => ['#3B82F6', '#10B981']
                ]
            ]
        ];

        foreach ($widgets as $widgetData) {
            $dashboard->widgets()->create(array_merge($widgetData, [
                'refresh_interval' => 600
            ]));
        }
    }
}
