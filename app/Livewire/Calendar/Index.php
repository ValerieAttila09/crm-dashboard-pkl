<?php

namespace App\Livewire\Calendar;

use Livewire\Component;
use App\Models\Lease;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Index extends Component
{
    public $currentMonth;
    public $currentYear;

    public function mount()
    {
        $this->currentMonth = Carbon::now()->month;
        $this->currentYear = Carbon::now()->year;
    }

    public function previousMonth()
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function nextMonth()
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function goToToday()
    {
        $this->currentMonth = Carbon::now()->month;
        $this->currentYear = Carbon::now()->year;
    }

    public function render()
    {
        $currentTeam = Auth::user()->currentTeam;
        $firstDayOfMonth = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        
        // Menentukan hari pertama dalam seminggu (0 = Minggu, 1 = Senin, dst)
        $startingDayOfWeek = $firstDayOfMonth->dayOfWeek;

        // Ambil data Lease milik tim aktif yang berakhir/jatuh tempo pada bulan & tahun ini
        $leases = Lease::where('team_id', $currentTeam->id)
            ->with(['room', 'tenant'])
            ->whereMonth('end_date', $this->currentMonth)
            ->whereYear('end_date', $this->currentYear)
            ->get();

        // Kelompokkan Event berdasarkan tanggal jatuh tempo (Format Y-m-d)
        $eventsByDate = [];

        foreach ($leases as $lease) {
            $dateKey = Carbon::parse($lease->end_date)->format('Y-m-d');
            $tenantName = $lease->tenant->name ?? 'Penyewa';
            $roomNumber = $lease->room->room_number ?? '-';

            $eventsByDate[$dateKey][] = [
                'type' => 'lease_due',
                'title' => "Jatuh Tempo: Kamar {$roomNumber} ({$tenantName})",
                'payment_status' => $lease->payment_status,
                'amount' => $lease->monthly_rent,
            ];
        }

        return view('livewire.calendar.index', [
            'monthName' => $firstDayOfMonth->translatedFormat('F Y'),
            'daysInMonth' => $daysInMonth,
            'startingDayOfWeek' => $startingDayOfWeek,
            'eventsByDate' => $eventsByDate,
        ])->layout('layouts.app');
    }
}