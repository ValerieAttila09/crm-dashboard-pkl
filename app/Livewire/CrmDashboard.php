<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Interaction;
use Illuminate\Support\Facades\DB;

class CrmDashboard extends Component
{
    public function render()
    {
        // 1. Metrik Utama
        $totalCustomers = Customer::count();
        $totalDealsValue = Deal::sum('amount') ?? 0;
        
        // Gunakan Raw Query yang aman terhadap tipe Enum/Varchar
        $wonDealsValue = Deal::whereRaw("LOWER(stage::text) = 'won'")->sum('amount') ?? 0;
        $activeDealsCount = Deal::whereRaw("LOWER(stage::text) NOT IN ('won', 'lost')")->count();

        // 2. Win Rate / Conversion Rate (%)
        $totalEndedDeals = Deal::whereRaw("LOWER(stage::text) IN ('won', 'lost')")->count();
        $wonDealsCount = Deal::whereRaw("LOWER(stage::text) = 'won'")->count();
        $winRate = $totalEndedDeals > 0 ? round(($wonDealsCount / $totalEndedDeals) * 100, 1) : 0;

        // 3. Breakdown Nilai per Stage
        $stageBreakdown = [
            'lead' => Deal::whereRaw("LOWER(stage::text) = 'lead'")->sum('amount') ?? 0,
            'proposal' => Deal::whereRaw("LOWER(stage::text) = 'proposal'")->sum('amount') ?? 0,
            'negotiation' => Deal::whereRaw("LOWER(stage::text) = 'negotiation'")->sum('amount') ?? 0,
            'won' => $wonDealsValue,
            'lost' => Deal::whereRaw("LOWER(stage::text) = 'lost'")->sum('amount') ?? 0,
        ];

        // 4. Aktivitas Terbaru
        $recentInteractions = Interaction::with('customer')
            ->latest()
            ->take(5)
            ->get();

        // 5. Pelanggan Terbaru
        $recentCustomers = Customer::latest()->take(5)->get();

        return view('livewire.crm-dashboard', [
            'totalCustomers' => $totalCustomers,
            'totalDealsValue' => $totalDealsValue,
            'wonDealsValue' => $wonDealsValue,
            'activeDealsCount' => $activeDealsCount,
            'winRate' => $winRate,
            'stageBreakdown' => $stageBreakdown,
            'recentInteractions' => $recentInteractions,
            'recentCustomers' => $recentCustomers,
        ])->layout('layouts.app');
    }
}