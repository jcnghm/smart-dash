<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-neutral-200 dark:border-neutral-700 h-full">
    
    {{-- Widget Header --}}
    <div class="flex justify-between items-center p-4 border-b border-neutral-200 dark:border-neutral-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $widget->title }}</h3>
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ $widget->type->getIcon() }} {{ $widget->type->getLabel() }}
            </span>
            <div class="w-2 h-2 bg-green-400 rounded-full"></div>
        </div>
    </div>
    
    {{-- Widget Content --}}
    <div class="p-4 h-full">
        @switch($widget->type)
            @case(App\Enums\WidgetType::COUNTER)
                <div class="flex flex-col items-center justify-center h-full">
                    <div class="text-3xl font-bold mb-2 {{ $this->getCounterColorClass($widget->config['color'] ?? 'blue') }}">
                        {{ $widget->config['prefix'] ?? '' }}{{ number_format($data) }}{{ $widget->config['suffix'] ?? '' }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $widget->title }}</div>
                </div>
                @break
                
            @case(App\Enums\WidgetType::CHART_LINE)
            @case(App\Enums\WidgetType::CHART_BAR) 
            @case(App\Enums\WidgetType::CHART_PIE)
            @case(App\Enums\WidgetType::CHART_AREA)
            @case(App\Enums\WidgetType::CHART_SCATTER)
                <div class="flex items-center justify-center h-full text-gray-400">
                    <div class="text-center">
                        <div class="text-4xl mb-2">{{ $widget->type->getIcon() }}</div>
                        <div class="text-sm">{{ $widget->type->getLabel() }}</div>
                        <div class="text-xs mt-1">Data source: {{ $widget->dataSource->name ?? 'Demo Data' }}</div>
                    </div>
                </div>
                @break
                
            @case(App\Enums\WidgetType::TABLE)
                <div class="flex items-center justify-center h-full text-gray-400">
                    <div class="text-center">
                        <div class="text-4xl mb-2">{{ $widget->type->getIcon() }}</div>
                        <div class="text-sm">{{ $widget->type->getLabel() }}</div>
                        <div class="text-xs mt-1">{{ $widget->dataSource->row_count ?? 0 }} rows</div>
                    </div>
                </div>
                @break
                
            @default
                <div class="flex items-center justify-center h-full text-gray-400">
                    <div class="text-center">
                        <div class="text-sm">Unknown widget type:</div>
                        <div class="font-mono text-xs">{{ $widget->type->value }}</div>
                    </div>
                </div>
        @endswitch
    </div>
</div>