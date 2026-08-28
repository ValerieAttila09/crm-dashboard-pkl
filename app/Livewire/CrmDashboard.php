<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Deal;

class CrmDashboard extends Component
{
    public function render()
    {
        return view('livewire.crm-dashboard', [
            'totalCustomers' => Customer::count(),
            'totalDealsValue' => Deal::sum('amount'),
            'recentCustomers' => Customer::latest()->take(5)->get(),
        ]);
    }
}