<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Dashboard;
use Illuminate\Support\Str;
use App\Services\RustServerApiClient;
use Illuminate\Support\Facades\{Auth, Log};
class DashboardOverview extends Component
{
    public $dashboards;
    public $apiResponse = null;
    public $apiError = null;

    public function mount()
    {
        $this->loadDashboards();
    }

    public function loadDashboards()
    {
        $this->dashboards = Dashboard::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function createNewDashboard()
    {
        $dashboard = Dashboard::create([
            'user_id' => Auth::id(),
            'uuid' => Str::uuid(),
            'name' => 'New Dashboard #' . ($this->dashboards->count() + 1),
            'description' => 'A new dashboard ready for customization',
            'is_public' => false,
            'grid_config' => ['columns' => 12, 'gap' => 4]
        ]);

        $this->loadDashboards();
        
        return redirect()->route('dashboards.show', $dashboard->uuid);
    }

    public function deleteDashboard($dashboardId)
    {
        $dashboard = Dashboard::where('user_id', Auth::id())->find($dashboardId);
        
        if ($dashboard && $this->dashboards->count() > 1) {
            $dashboard->delete();
            $this->loadDashboards();
            $this->dispatch('dashboard-deleted');
        }
    }

    public function testApiEndpoint()
    {
        $this->clearApiResponse();
    
        try {
            $client = new RustServerApiClient();
            $response = $client->getApiHealthCheck();
        
            if ($response['success']) {
                $this->apiResponse = $response['message'];
                $this->apiError = null;
                Log::info('Set SUCCESS', ['apiResponse' => $this->apiResponse]);
            } else {
                $this->apiError = $response['message'];
                $this->apiResponse = null;
                Log::info('Set ERROR', ['apiError' => $this->apiError]);
            }
        } catch (\Exception $e) {
            $this->apiError = $e->getMessage();
            $this->apiResponse = null;
            Log::error('API Exception', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }

    public function clearApiResponse()
    {
        $this->apiResponse = null;
        $this->apiError = null;
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-overview');
    }
}