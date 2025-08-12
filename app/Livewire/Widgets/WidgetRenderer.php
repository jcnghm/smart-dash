<?php

namespace App\Livewire\Widgets;

use Livewire\Component;
use App\Models\Widget;
use App\Enums\WidgetType;

class WidgetRenderer extends Component
{
    public Widget $widget;
    public $data = [];

    public function mount(Widget $widget)
    {
        $this->widget = $widget;
        $this->loadData();
    }

    public function loadData()
    {
        // TODO
        $this->data = $this->widget->config['demo_value'] ?? 'N/A';
    }

    public function getCounterColorClass($color)
    {
        return match($color) {
            'blue' => 'text-blue-600',
            'green' => 'text-green-600',
            'purple' => 'text-purple-600',
            'orange' => 'text-orange-600',
            'red' => 'text-red-600',
            default => 'text-blue-600'
        };
    }

    public function render()
    {
        return view('livewire.widgets.widget-renderer');
    }
}