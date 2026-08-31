<?php

namespace App\Livewire\Calendar;

use Livewire\Component;
use App\Models\Task;
use App\Models\Deal;
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

        // Ambil Tasks milik tim aktif pada bulan & tahun ini
        $tasks = Task::whereHas('deal', function ($query) use ($currentTeam) {
                $query->where('team_id', $currentTeam->id);
            })
            ->whereMonth('due_date', $this->currentMonth)
            ->whereYear('due_date', $this->currentYear)
            ->get();

        // Ambil Deals milik tim aktif yang punya expected_close_date pada bulan & tahun ini
        $deals = Deal::where('team_id', $currentTeam->id)
            ->whereNotNull('expected_close_date')
            ->whereMonth('expected_close_date', $this->currentMonth)
            ->whereYear('expected_close_date', $this->currentYear)
            ->get();

        // Kelompokkan Event berdasarkan tanggal (Format Y-m-d)
        $eventsByDate = [];

        foreach ($tasks as $task) {
            $dateKey = Carbon::parse($task->due_date)->format('Y-m-d');
            $eventsByDate[$dateKey][] = [
                'type' => 'task',
                'title' => $task->title,
                'is_completed' => $task->is_completed,
            ];
        }

        foreach ($deals as $deal) {
            $dateKey = Carbon::parse($deal->expected_close_date)->format('Y-m-d');
            $eventsByDate[$dateKey][] = [
                'type' => 'deal',
                'title' => 'Closing: ' . $deal->title,
                'amount' => $deal->amount,
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