<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Deal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CrmDashboard extends Component
{
    public function render()
    {
        // 1. Data Breakdown per Stage (Doughnut Chart)
        $stageCounts = Deal::select('stage', DB::raw('count(*) as total'))
            ->groupBy('stage')
            ->pluck('total', 'stage')
            ->toArray();

        $stages = ['Lead', 'Proposal', 'Negotiation', 'Won', 'Lost'];
        $stageChartData = [];
        foreach ($stages as $stage) {
            $stageChartData[] = $stageCounts[$stage] 
                ?? $stageCounts[strtolower($stage)] 
                ?? $stageCounts[strtoupper($stage)] 
                ?? 0;
        }

        // 2. Data Trend Won Deals (Line Chart)
        $monthsLabels = [];
        $revenueData = [];

        // Ambil data 6 bulan terakhir dari database
        $monthlyRevenue = Deal::select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month_key"),
                DB::raw("SUM(amount) as total_amount")
            )
            ->whereIn(DB::raw('LOWER(stage)'), ['won'])
            ->groupBy('month_key')
            ->orderBy('month_key', 'asc')
            ->pluck('total_amount', 'month_key')
            ->toArray();

        // Generasi 6 Bulan Terakhir (termasuk bulan ini) agar grafik tidak kosong
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $key = $date->format('Y-m');
            
            $monthsLabels[] = $date->format('M Y');
            $revenueData[] = isset($monthlyRevenue[$key]) ? (float) $monthlyRevenue[$key] : 0;
        }

        return view('livewire.crm-dashboard', [
            'stageLabels' => $stages,
            'stageChartData' => $stageChartData,
            'monthsLabels' => $monthsLabels,
            'revenueData' => $revenueData,
        ])->layout('layouts.app');
    }
}