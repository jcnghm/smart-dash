<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    
    {{-- Dashboard Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $dashboard->name }}</h1>
            @if($dashboard->description)
                <p class="text-gray-600 dark:text-gray-400">{{ $dashboard->description }}</p>
            @endif
        </div>
        
        <div class="flex gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Add Widget
            </button>
            <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300">
                Settings
            </button>
        </div>
    </div>

    {{-- Widgets Grid --}}
    <div class="grid grid-cols-12 gap-4 auto-rows-min">
        @forelse($dashboard->widgets as $widget)
            <div class="widget-container" 
                 style="--grid-col-span: {{ $widget->width }}; --grid-row-span: {{ $widget->height }}; grid-column: span var(--grid-col-span); grid-row: span var(--grid-row-span);">
                <livewire:widgets.widget-renderer 
                    :widget="$widget" 
                    :key="'widget-'.$widget->id" />
            </div>
        @empty
            {{-- Empty State --}}
            <div class="col-span-12">
                <div class="relative h-64 overflow-hidden rounded-xl border-2 border-dashed border-neutral-200 dark:border-neutral-700">
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-8">
                        <div class="text-4xl mb-4">📊</div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            No widgets yet
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Start building your dashboard by adding your first widget
                        </p>
                        <button class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Add Your First Widget
                        </button>
                    </div>
                    <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/10 dark:stroke-neutral-100/10" />
                </div>
            </div>
        @endforelse
    </div>

</div>