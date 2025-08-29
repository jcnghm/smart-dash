<?php

namespace App\Livewire\Dashboard;

use Exception;
use Livewire\Component;
use App\Models\Dashboard;
use App\Models\Employee;
use Illuminate\Support\Str;
use App\Services\RustServerApiClient;
use App\Jobs\SyncEmployeeData;
use Livewire\WithPagination;
use Illuminate\Support\Facades\{Auth, Log};

class DashboardOverview extends Component
{
    use WithPagination;

    public $dashboards;
    public $apiResponse = null;
    public $apiError = null;
    public $jobStatus = null;
    public $isProcessing = false;
    public $employeeCount = 0;
    public $unsyncedEmployeeCount = 0;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->loadDashboards();
        $this->loadEmployeeCounts();
    }

    public function loadDashboards()
    {
        $this->dashboards = Dashboard::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function loadEmployeeCounts()
    {
        $this->employeeCount = Employee::count();
        $this->unsyncedEmployeeCount = Employee::whereNull('synced_at')->count();
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
                Log::info('API Health Check Success', ['response' => $this->apiResponse]);
            } else {
                $this->apiError = $response['message'];
                $this->apiResponse = null;
                Log::info('API Health Check Error', ['error' => $this->apiError]);
            }
        } catch (\Exception $e) {
            $this->apiError = $e->getMessage();
            $this->apiResponse = null;
            Log::error('API Health Check Exception', [
                'error' => $e->getMessage(), 
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function pushEmployeeData()
    {
        try {
            $this->isProcessing = true;
            $this->jobStatus = null;

            if ($this->unsyncedEmployeeCount === 0) {
                $this->jobStatus = 'No unsynced employees found to push to Rust API.';
                $this->isProcessing = false;
                return;
            }
            
            $meta = [
                'triggered_from' => 'dashboard_overview',
                'triggered_by' => Auth::id(),
                'timestamp' => now(),
                'total_unsynced_employees' => $this->unsyncedEmployeeCount
            ];

            SyncEmployeeData::dispatch(Auth::id(), $meta);

            $this->jobStatus = "Job dispatched successfully! Pushing {$this->unsyncedEmployeeCount} unsynced employees to Rust API...";
            
            Log::info('Employee data push job dispatched', array_merge($meta, [
                'job_id' => 'unknown'
            ]));

            $this->loadEmployeeCounts();

            $this->dispatch('job-dispatched', [
                'type' => 'employee-push',
                'count' => $this->unsyncedEmployeeCount
            ]);

        } catch (Exception $e) {
            $this->jobStatus = 'Error dispatching job: ' . $e->getMessage();
            Log::error('Failed to dispatch employee data push job', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            $this->isProcessing = false;
        }
    }

    public function clearJobStatus()
    {
        $this->jobStatus = null;
    }

    public function clearApiResponse()
    {
        $this->apiResponse = null;
        $this->apiError = null;
    }

    public function loadEmployees()
    {
        return Employee::select([
            'id', 'guid', 'first_name', 'last_name', 'email', 
            'manager_id', 'synced_at', 'created_at', 'updated_at'
        ])
        ->orderBy('id', 'desc')
        ->paginate(15);
    }

    public function getEmployeeStatsProperty()
    {
        return [
            'total' => $this->employeeCount,
            'unsynced' => $this->unsyncedEmployeeCount,
            'synced' => $this->employeeCount - $this->unsyncedEmployeeCount,
            'sync_percentage' => $this->employeeCount > 0 
                ? round((($this->employeeCount - $this->unsyncedEmployeeCount) / $this->employeeCount) * 100, 1) 
                : 0
        ];
    }
    
    public function render()
    {
        return view('livewire.dashboard.dashboard-overview', [
            'employees' => $this->loadEmployees(),
            'employeeStats' => $this->getEmployeeStatsProperty()
        ]);
    }
}