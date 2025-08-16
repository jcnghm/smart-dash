<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Dashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardView extends Component
{
    public Dashboard $dashboard;

    public function mount(Dashboard $dashboard = null)
    {
        if ($dashboard) {
            if ($dashboard->user_id !== Auth::id() && !$dashboard->is_public) {
                abort(403, 'Unauthorized access to dashboard');
            }

            $this->dashboard = $dashboard->load(['widgets' => function ($query) {
                $query->orderBy('position_y')->orderBy('position_x');
            }]);
        } else {
            // TODO REMOVE THIS FALLBACK: get user's first dashboard or create default
            $this->dashboard = Dashboard::where('user_id', Auth::id())->first()
                ?? $this->createDefaultDashboard();
        }
    }

    private function createDefaultDashboard()
    {
        return Dashboard::create([
            'user_id' => Auth::id(),
            'uuid' => Str::uuid(),
            'name' => 'My Dashboard',
            'description' => 'Default dashboard',
            'is_public' => false,
            'grid_config' => ['columns' => 12, 'gap' => 4]
        ]);
    }

    public function getWidgetGridStyle($widget)
    {
        return "grid-column: span {$widget->width}; grid-row: span {$widget->height};";
    }

    public function backToDashboards()
    {
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-view', [
            'title' => $this->dashboard->name
        ]);
    }
}
