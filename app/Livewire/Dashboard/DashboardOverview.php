<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Dashboard;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{Auth, Log};
use App\Services\ApiService;


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
        
        // if ($this->dashboards->isEmpty()) {
        //     $this->createDefaultDashboard();
        //     $this->loadDashboards();
        // }
    }

    public function createDefaultDashboard()
    {
        Dashboard::create([
            'user_id' => Auth::id(),
            'uuid' => Str::uuid(),
            'name' => 'My First Dashboard',
            'description' => 'Welcome to your first dashboard!',
            'is_public' => false,
            'grid_config' => ['columns' => 12, 'gap' => 4]
        ]);

        $this->dispatch('dashboard-created');
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
        try {
            $this->clearApiResponse();

            $client = new ApiService();

            $response = $client->getApiHealthCheck();

            if ($response['success'] ?? null) {
                $this->apiResponse = $response['message'];
            } else {
                $this->apiError = "API returned status code: {$response->status()}";
                Log::error('API Error', ['status' => $response->status(), 'body' => $response->body()]);
            }

        } catch (\Exception $e) {
            $this->apiError = "Failed to connect to API: " . $e->getMessage();
            Log::error('API Exception', ['error' => $e->getMessage()]);
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