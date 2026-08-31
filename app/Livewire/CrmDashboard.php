<?php
// app/Livewire/CrmDashboard.php

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
        $currentTeam = Auth::user()->currentTeam;

        // 1. Data Breakdown per Stage (Doughnut Chart) HANYA untuk Tim Aktif
        $stageCounts = Deal::where('team_id', $currentTeam->id)
            ->select('stage', DB::raw('count(*) as total'))
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

        // 2. Data Trend Won Deals (Line Chart) HANYA untuk Tim Aktif
        $monthlyRevenue = Deal::where('team_id', $currentTeam->id)
            ->select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month_key"),
                DB::raw("SUM(amount) as total_amount")
            )
            ->whereIn(DB::raw('LOWER(stage)'), ['won'])
            ->groupBy('month_key')
            ->orderBy('month_key', 'asc')
            ->pluck('total_amount', 'month_key')
            ->toArray();

        $monthsLabels = [];
        $revenueData = [];

        // Generasi 6 Bulan Terakhir
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