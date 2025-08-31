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
    public $api_response = null;
    public $api_error = null;
    public $job_status = null;
    public $is_processing = false;
    public $employee_count = 0;
    public $unsynced_count = 0;

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
        $this->employee_count = Employee::count();
        $this->unsynced_count = Employee::whereNull('synced_at')->count();
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
                $this->api_response = $response['message'];
                $this->api_error = null;
                Log::info('API Health Check Success', ['response' => $this->api_response]);
            } else {
                $this->api_error = $response['message'];
                $this->api_response = null;
                Log::info('API Health Check Error', ['error' => $this->api_error]);
            }
        } catch (\Exception $e) {
            $this->api_error = $e->getMessage();
            $this->api_response = null;
            Log::error('API Health Check Exception', [
                'error' => $e->getMessage(), 
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function pushEmployeeData()
    {
        try {
            $this->is_processing = true;
            $this->job_status = null;

            if ($this->unsyncedEmployeeCount === 0) {
                $this->job_status = 'No unsynced employees found to push to Rust API.';
                $this->is_processing = false;
                return;
            }
            
            $meta = [
                'triggered_from' => 'dashboard_overview',
                'triggered_by' => Auth::id(),
                'timestamp' => now(),
                'total_unsynced_employees' => $this->unsyncedEmployeeCount
            ];

            SyncEmployeeData::dispatch(Auth::id(), $meta);

            $this->job_status = "Job dispatched successfully! Pushing {$this->unsyncedEmployeeCount} unsynced employees to Rust API...";
            
            Log::info('Employee data push job dispatched', array_merge($meta, [
                'job_id' => 'unknown'
            ]));

            $this->loadEmployeeCounts();

            $this->dispatch('job-dispatched', [
                'type' => 'employee-push',
                'count' => $this->unsyncedEmployeeCount
            ]);

        } catch (Exception $e) {
            $this->job_status = 'Error dispatching job: ' . $e->getMessage();
            Log::error('Failed to dispatch employee data push job', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            $this->is_processing = false;
        }
    }

    public function clearJobStatus()
    {
        $this->job_status = null;
    }

    public function clearApiResponse()
    {
        $this->api_response = null;
        $this->api_error = null;
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
            'total' => $this->employee_count,
            'unsynced' => $this->unsynced_count,
            'synced' => $this->employee_count - $this->unsynced_count,
            'sync_percentage' => $this->employee_count > 0 
                ? round((($this->employee_count - $this->unsynced_count) / $this->employee_count) * 100, 1) 
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