<?php

namespace App\Livewire\Calendar;

use Livewire\Component;
use App\Models\Property;
use App\Models\Room;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Index extends Component
{
    public $viewMode = 'today'; // 'today', 'week', 'month'
    public $selectedDate;
    
    // Form Modal State
    public $isModalOpen = false;
    public $title;
    public $property_id;
    public $room_id;
    public $schedule_date;
    public $start_time = '09:00';
    public $end_time = '10:00';
    public $type = 'survey';
    public $status = 'upcoming';
    public $notes;

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'property_id' => 'required|exists:properties,id',
            'room_id' => 'nullable|exists:rooms,id',
            'schedule_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'type' => 'required|in:checkup,maintenance,survey,lease_due,other',
            'status' => 'required|in:upcoming,done,cancelled',
            'notes' => 'nullable|string',
        ];
    }

    public function mount()
    {
        $this->selectedDate = Carbon::today()->format('Y-m-d');
        $this->schedule_date = Carbon::today()->format('Y-m-d');
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function goToToday()
    {
        $this->selectedDate = Carbon::today()->format('Y-m-d');
    }

    public function openCreateModal()
    {
        $this->resetInputFields();
        $this->schedule_date = $this->selectedDate;
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->title = '';
        $this->property_id = null;
        $this->room_id = null;
        $this->start_time = '09:00';
        $this->end_time = '10:00';
        $this->type = 'survey';
        $this->status = 'upcoming';
        $this->notes = '';
        $this->resetValidation();
    }

    public function storeSchedule()
    {
        $this->validate();
        $teamId = Auth::user()->currentTeam->id;

        $startDateTime = Carbon::parse("{$this->schedule_date} {$this->start_time}");
        $endDateTime = Carbon::parse("{$this->schedule_date} {$this->end_time}");

        Schedule::create([
            'team_id' => $teamId,
            'property_id' => $this->property_id,
            'room_id' => $this->room_id,
            'title' => $this->title,
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'type' => $this->type,
            'status' => $this->status,
            'notes' => $this->notes,
        ]);

        session()->flash('message', 'Jadwal baru berhasil ditambahkan.');
        $this->closeModal();
    }

    public function updateStatus($scheduleId, $newStatus)
    {
        $schedule = Schedule::where('team_id', Auth::user()->currentTeam->id)->findOrFail($scheduleId);
        $schedule->update(['status' => $newStatus]);
    }

    public function render()
    {
        $currentTeam = Auth::user()->currentTeam;
        $activeDate = Carbon::parse($this->selectedDate);

        // 1. Tentukan Range Tanggal berdasarkan Mode Tampilan
        if ($this->viewMode === 'today') {
            $startDate = $activeDate->copy()->startOfDay();
            $endDate = $activeDate->copy()->endOfDay();
            $formattedHeader = $activeDate->translatedFormat('D, d F Y');
        } elseif ($this->viewMode === 'week') {
            $startDate = $activeDate->copy()->startOfWeek();
            $endDate = $activeDate->copy()->endOfWeek();
            $formattedHeader = "Minggu (" . $startDate->translatedFormat('d M') . " - " . $endDate->translatedFormat('d M Y') . ")";
        } else { // month
            $startDate = $activeDate->copy()->startOfMonth();
            $endDate = $activeDate->copy()->endOfMonth();
            $formattedHeader = $activeDate->translatedFormat('F Y');
        }

        // 2. Query Properties & Rooms
        $properties = Property::where('team_id', $currentTeam->id)->with('rooms')->get();
        $rooms = Room::where('team_id', $currentTeam->id)->get();

        // 3. Query Schedule dari Database
        $schedules = Schedule::where('team_id', $currentTeam->id)
            ->with(['property', 'room'])
            ->whereBetween('start_time', [$startDate, $endDate])
            ->get();

        // Slot jam vertikal (08:00 sampai 18:00)
        $timeSlots = [
            '08:00', '09:00', '10:00', '11:00', '12:00', 
            '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'
        ];

        // Daftar Hari dalam Minggu jika Mode 'Week'
        $weekDays = [];
        if ($this->viewMode === 'week') {
            for ($i = 0; $i < 7; $i++) {
                $weekDays[] = $startDate->copy()->addDays($i);
            }
        }

        return view('livewire.calendar.index', [
            'properties' => $properties,
            'rooms' => $rooms,
            'schedules' => $schedules,
            'timeSlots' => $timeSlots,
            'weekDays' => $weekDays,
            'formattedHeader' => $formattedHeader,
            'currentTime' => Carbon::now()->format('H:i'),
        ])->layout('layouts.app');
    }
}