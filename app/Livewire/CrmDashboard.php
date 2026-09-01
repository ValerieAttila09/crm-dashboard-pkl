<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Room;
use App\Models\Property;
use App\Models\Lease;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CrmDashboard extends Component
{
    public int $totalProperties = 0;
    public int $totalRooms = 0;
    public int $occupiedRooms = 0;
    public int $availableRooms = 0;
    public float $occupancyRate = 0;
    public float $monthlyRevenueTarget = 0;

    public function render()
    {
        $currentTeam = Auth::user()->currentTeam;

        // 1. Data Ringkasan Stat (Metric Cards)
        $this->totalProperties = Property::where('team_id', $currentTeam->id)->count();
        $this->totalRooms = Room::where('team_id', $currentTeam->id)->count();
        $this->occupiedRooms = Room::where('team_id', $currentTeam->id)->where('status', 'occupied')->count();
        $this->availableRooms = Room::where('team_id', $currentTeam->id)->where('status', 'available')->count();

        $this->occupancyRate = $this->totalRooms > 0 ? round(($this->occupiedRooms / $this->totalRooms) * 100, 1) : 0;

        $this->monthlyRevenueTarget = (float) Lease::where('team_id', $currentTeam->id)
            ->where('status', 'active')
            ->where('payment_status', 'paid')
            ->sum('monthly_rent');

        // 2. Data Breakdown Status Kamar (Doughnut Chart)
        $roomCounts = Room::where('team_id', $currentTeam->id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $stageLabels = ['Tersedia (Available)', 'Terisi (Occupied)', 'Perawatan (Maintenance)'];
        $stageChartData = [
            $roomCounts['available'] ?? 0,
            $roomCounts['occupied'] ?? 0,
            $roomCounts['maintenance'] ?? 0,
        ];

        // 3. Data Trend Pendapatan Sewa 6 Bulan Terakhir (Line Chart)
        $monthlyRevenue = Lease::where('team_id', $currentTeam->id)
            ->where('payment_status', 'paid')
            ->select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month_key"),
                DB::raw("SUM(monthly_rent) as total_amount")
            )
            ->groupBy('month_key')
            ->orderBy('month_key', 'asc')
            ->pluck('total_amount', 'month_key')
            ->toArray();

        $monthsLabels = [];
        $revenueData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $key = $date->format('Y-m');
            
            $monthsLabels[] = $date->format('M Y');
            $revenueData[] = isset($monthlyRevenue[$key]) ? (float) $monthlyRevenue[$key] : 0;
        }

        return view('livewire.crm-dashboard', [
            'totalProperties' => $this->totalProperties,
            'totalRooms' => $this->totalRooms,
            'occupiedRooms' => $this->occupiedRooms,
            'availableRooms' => $this->availableRooms,
            'occupancyRate' => $this->occupancyRate,
            'monthlyRevenueTarget' => $this->monthlyRevenueTarget,
            'stageLabels' => $stageLabels,
            'stageChartData' => $stageChartData,
            'monthsLabels' => $monthsLabels,
            'revenueData' => $revenueData,
        ])->layout('layouts.app');
    }
}