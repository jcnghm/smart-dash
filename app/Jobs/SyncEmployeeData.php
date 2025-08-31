<?php

namespace App\Jobs;

use App\Models\Employee;
use Illuminate\Support\Arr;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Services\RustServerApiClient;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncEmployeeData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected iterable $employee_ids;

    public function __construct(
        protected RustServerApiClient $client, 
        protected string $user_id, 
        protected array $parameters = []
    ){}

    public function handle(): void
    {
        try {
            Log::info('Push employee data to Rust API job started', [
                'user_id' => $this->user_id,
                'employee_ids' => $this->employee_ids,
                'parameters' => $this->parameters
            ]);
    
            $employees = Employee::whereNull('synced_at')->get();
            
            if ($employees->isEmpty()) {
                Log::info('No employees found to push', ['user_id' => $this->user_id]);
                return;
            }
            
            $data = $employees->map(function (Employee $employee) {
                return [
                    'external_id' => (string) $employee->id,
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name,
                    'store_id' => (int) $employee->store_id,
                    'email' => $employee->email,
                ];
            })->toArray();
            
            $response = $this->client->postEmployeeData([
                'employees' => $data
            ]);
            
            $success_count = count(Arr::get($response, 'data.employees', []));
            
            $employee_ids = $employees->pluck('id');

            if ($success_count > 0) {
                Employee::whereIn('id', $employee_ids)->update([
                    'synced_at' => now(),
                ]);            
            }
        } catch (\Exception $e) {
            Log::error('Employee data push job failed', [
                'user_id' => $this->user_id,
                'employee_ids' => $this->employee_ids,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $employees->each->update(['synced_at' => null]);
    
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Employee data push job failed permanently', [
            'user_id' => $this->user_id,
            'employee_ids' => $this->employee_ids,
            'exception' => $exception->getMessage()
        ]);
    }
}