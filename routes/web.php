<?php

use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;
use App\Livewire\Customers\Index as CustomerIndex;
use App\Livewire\Deals\Index as DealIndex;
use App\Livewire\Customers\Show as CustomerShow;
use App\Livewire\Interactions\Index as InteractionIndex;
use App\Livewire\Teams\Settings as TeamSettings;
use App\Livewire\Tasks\Index as TaskIndex;
use App\Http\Controllers\ExportController;

Route::view('/', 'welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');        
        Route::get('customers', CustomerIndex::class)->name('customers.index');
        Route::get('customers/{id}', CustomerShow::class)->name('customers.show'); 
        Route::get('deals', DealIndex::class)->name('deals.index');
        Route::get('activities', InteractionIndex::class)->name('interactions.index');
        Route::get('team/settings', TeamSettings::class)->name('team.settings');
        Route::get('tasks', TaskIndex::class)->name('tasks.index');

        // ROUTE EXPORT DATA
        Route::get('export/customers', [ExportController::class, 'exportCustomers'])->name('export.customers');
        Route::get('export/deals', [ExportController::class, 'exportDeals'])->name('export.deals');
        Route::get('export/tasks', [ExportController::class, 'exportTasks'])->name('export.tasks');
    });

require __DIR__.'/settings.php';