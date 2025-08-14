<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Dashboard\DashboardOverview;
use App\Livewire\Dashboard\DashboardView;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Dashboard Overview Page - shows all dashboards
Route::get('/dashboard', DashboardOverview::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Individual Dashboard View - shows specific dashboard by UUID
Route::get('/dashboards/{dashboard}', DashboardView::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboards.show');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::get('/debug', function () {
    return include_once(__DIR__ . '/debug.php');
});

require __DIR__ . '/auth.php';
