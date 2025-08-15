<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">My Dashboards</h1>
            <p class="text-gray-300 mt-1">Manage and access your dashboards</p>
        </div>

        <div class="flex items-center gap-3">
            {{-- API Test Button --}}
            <button 
                wire:click="testApiEndpoint"
                wire:loading.attr="disabled"
                class="inline-flex justify-center items-center px-6 py-3 bg-red-600 border border-transparent rounded-full text-sm font-semibold text-white uppercase tracking-widest hover:bg-red-700 hover:shadow-lg hover:scale-105 active:bg-red-800 active:scale-95 focus:outline-none focus:ring-4 focus:ring-red-300 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer transition-all duration-200 shadow-md"
            >
                <div wire:loading wire:target="testApiEndpoint" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                <svg wire:loading.remove wire:target="testApiEndpoint" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <span wire:loading.remove wire:target="testApiEndpoint">Test API</span>
                <span wire:loading wire:target="testApiEndpoint">Loading...</span>
            </button>

            {{-- Create Dashboard Button --}}
            <button 
                wire:click="createNewDashboard"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-200 flex items-center gap-2 cursor-pointer focus:outline-none focus:ring-4 focus:ring-red-300 font-medium shadow-md"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Dashboard
            </button>
        </div>
    </div>

    {{-- API Response Display --}}
    @if($apiResponse || $apiError)
        <div class="bg-gray-900 rounded-lg border border-gray-700 p-4">
           
            
            @if($apiError)
            <div class="flex justify-between items-start mb-3">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    API Response
                </h3>
                <button 
                    wire:click="clearApiResponse"
                    class="text-gray-400 hover:text-gray-300 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
                <div class="flex items-start gap-3 text-red-300">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-red-400 mb-1">Error</h4>
                        <pre class="text-sm text-gray-200 whitespace-pre-wrap">{{ json_encode($apiError, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            @else
            <div class="flex justify-between items-start mb-3">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    API Response
                </h3>
                <button 
                    wire:click="clearApiResponse"
                    class="text-gray-400 hover:text-gray-300 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
                <div class="bg-black rounded-lg p-4 max-h-96 overflow-auto">
                    <pre class="text-sm text-gray-200 whitespace-pre-wrap">{{ json_encode($apiResponse, JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif
        </div>
    @endif

    {{-- Quick Stats --}}
    @if($dashboards->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-900 rounded-lg border border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-red-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-400">Total Dashboards</p>
                        <p class="text-2xl font-semibold text-white">{{ $dashboards->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-900 rounded-lg border border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-red-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-400">Total Widgets</p>
                        <p class="text-2xl font-semibold text-white">{{ $dashboards->sum(function($d) { return $d->widgets->count(); }) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-900 rounded-lg border border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-red-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-400">Public Dashboards</p>
                        <p class="text-2xl font-semibold text-white">{{ $dashboards->where('is_public', true)->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Dashboards Grid --}}
    <div class="grid auto-rows-min gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($dashboards as $dashboard)
            <div class="group relative overflow-hidden rounded-xl border border-gray-700 bg-gray-900 hover:shadow-lg transition-all duration-200 hover:border-red-600">
                
                {{-- Dashboard Preview/Thumbnail --}}
                <div class="relative aspect-video overflow-hidden bg-gradient-to-br from-gray-800 to-black">
                    @if($dashboard->widgets->count() > 0)
                        {{-- Mini Dashboard Preview --}}
                        <div class="absolute inset-3 flex flex-wrap content-start gap-1.5">
                            @foreach($dashboard->widgets->take(8) as $widget)
                                @php
                                    $widgetType = $widget->type ?? 'default';
                                @endphp
                                <div class="bg-gray-800 bg-opacity-95 rounded shadow border border-gray-600 p-1.5 flex-shrink-0" 
                                     style="width: 52px; height: 36px;">
                                    {{-- Widget content preview --}}
                                    @if($widgetType === 'chart')
                                        <div class="w-full h-full flex items-end justify-between px-1">
                                            <div class="w-2 bg-red-500 rounded-sm" style="height: 50%;"></div>
                                            <div class="w-2 bg-red-500 rounded-sm" style="height: 85%;"></div>
                                            <div class="w-2 bg-red-400 rounded-sm" style="height: 30%;"></div>
                                            <div class="w-2 bg-red-500 rounded-sm" style="height: 70%;"></div>
                                            <div class="w-2 bg-red-400 rounded-sm" style="height: 60%;"></div>
                                        </div>
                                    @elseif($widgetType === 'table')
                                        <div class="w-full h-full flex flex-col justify-around px-1">
                                            <div class="h-1.5 bg-red-500 rounded-sm w-full"></div>
                                            <div class="h-1 bg-red-400 rounded-sm w-4/5"></div>
                                            <div class="h-1 bg-red-400 rounded-sm w-full"></div>
                                            <div class="h-1 bg-red-400 rounded-sm w-3/4"></div>
                                            <div class="h-1 bg-red-400 rounded-sm w-5/6"></div>
                                        </div>
                                    @elseif(in_array($widgetType, ['metric', 'counter']))
                                        <div class="w-full h-full flex flex-col items-center justify-center space-y-1">
                                            <div class="w-8 h-3 bg-red-500 rounded text-xs flex items-center justify-center">
                                                <span class="text-white text-xs font-bold">42</span>
                                            </div>
                                            <div class="w-6 h-1 bg-red-300 rounded-sm"></div>
                                        </div>
                                    @elseif(in_array($widgetType, ['text', 'note']))
                                        <div class="w-full h-full flex flex-col justify-around px-1">
                                            <div class="h-1 bg-red-500 rounded-sm w-full"></div>
                                            <div class="h-0.5 bg-red-400 rounded-sm w-4/5"></div>
                                            <div class="h-0.5 bg-red-400 rounded-sm w-full"></div>
                                            <div class="h-0.5 bg-red-400 rounded-sm w-2/3"></div>
                                            <div class="h-0.5 bg-red-400 rounded-sm w-5/6"></div>
                                            <div class="h-0.5 bg-red-400 rounded-sm w-3/4"></div>
                                        </div>
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <div class="w-4 h-4 bg-gray-500 rounded"></div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            
                            {{-- Add some empty placeholder widgets if less than 6 --}}
                            @if($dashboard->widgets->count() < 6)
                                @for($i = $dashboard->widgets->count(); $i < min(6, $dashboard->widgets->count() + 2); $i++)
                                    <div class="bg-gray-600 bg-opacity-30 rounded border border-dashed border-gray-500 flex items-center justify-center flex-shrink-0" 
                                         style="width: 52px; height: 36px;">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </div>
                                @endfor
                            @endif
                        </div>
                        
                        {{-- Overflow indicator --}}
                        @if($dashboard->widgets->count() > 8)
                            <div class="absolute bottom-2 right-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-900 bg-opacity-80 text-white">
                                    +{{ $dashboard->widgets->count() - 8 }}
                                </span>
                            </div>
                        @endif
                    @else
                        {{-- Empty state preview --}}
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <div class="w-20 h-16 mx-auto mb-2 rounded-lg border-2 border-dashed border-gray-500 flex items-center justify-center bg-gray-800 bg-opacity-50">
                                    <svg class="w-10 h-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-400 font-medium">Empty Dashboard</p>
                            </div>
                        </div>
                    @endif
                    
                    {{-- Widget Count Badge --}}
                    <div class="absolute top-2 right-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-600 text-white shadow-sm">
                            {{ $dashboard->widgets->count() }}
                        </span>
                    </div>

                    {{-- Quick Action Overlay --}}
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <a 
                            href="{{ route('dashboards.show', $dashboard) }}"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors duration-200 transform scale-95 group-hover:scale-100"
                        >
                            Open Dashboard
                        </a>
                    </div>
                </div>

                {{-- Dashboard Info --}}
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-semibold text-white truncate">
                            {{ $dashboard->name }}
                        </h3>
                        
                        {{-- Actions Dropdown --}}
                        <div class="relative" x-data="{ open: false }">
                            <button 
                                @click="open = !open"
                                class="p-1 rounded-lg hover:bg-gray-700 transition-colors duration-200"
                            >
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                </svg>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-md shadow-lg z-10 border border-gray-700">
                                <div class="py-1">
                                    <a 
                                        href="{{ route('dashboards.show', $dashboard) }}" 
                                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700"
                                    >
                                        Edit Dashboard
                                    </a>
                                    @if($dashboards->count() > 1)
                                        <button 
                                            wire:click="deleteDashboard({{ $dashboard->id }})"
                                            onclick="return confirm('Are you sure you want to delete this dashboard?')"
                                            class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-gray-700"
                                        >
                                            Delete Dashboard
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($dashboard->description)
                        <p class="text-gray-400 text-sm mb-3 line-clamp-2">
                            {{ $dashboard->description }}
                        </p>
                    @endif
                    
                    <div class="flex justify-between items-center text-xs text-gray-500">
                        <span>Updated {{ $dashboard->updated_at->diffForHumans() }}</span>
                        @if($dashboard->is_public)
                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-red-900 text-red-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                </svg>
                                Public
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-gray-700 text-gray-300">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                                Private
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty State (should rarely show due to auto-creation) --}}
            <div class="col-span-full">
                <div class="text-center py-12">
                    <h3 class="mt-2 text-sm font-medium text-white">No dashboards</h3>
                </div>
            </div>
        @endforelse
    </div>
</div>

@script
<script>
document.addEventListener('livewire:init', function () {
    Livewire.on('dashboard-created', () => {
        // You can add a toast notification here if you have one
        console.log('Dashboard created successfully');
    });
    
    Livewire.on('dashboard-deleted', () => {
        // You can add a toast notification here if you have one  
        console.log('Dashboard deleted successfully');
    });
});
</script>
@endscript