<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $priorityFilter = '';

    // Form Modal Fields
    public $isModalOpen = false;
    public $requestId = null;
    public $room_id, $customer_id, $title, $description, $priority = 'medium', $status = 'pending', $cost = 0;

    protected $rules = [
        'room_id' => 'required|exists:rooms,id',
        'customer_id' => 'nullable|exists:customers,id',
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'priority' => 'required|in:low,medium,high,urgent',
        'status' => 'required|in:pending,in_progress,completed,cancelled',
        'cost' => 'nullable|numeric|min:0',
    ];

    public function create()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->requestId = null;
        $this->room_id = '';
        $this->customer_id = '';
        $this->title = '';
        $this->description = '';
        $this->priority = 'medium';
        $this->status = 'pending';
        $this->cost = 0;
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();
        $currentTeam = Auth::user()->currentTeam;

        $request = MaintenanceRequest::updateOrCreate(
            ['id' => $this->requestId],
            [
                'team_id' => $currentTeam->id,
                'room_id' => $this->room_id,
                'customer_id' => $this->customer_id ?: null,
                'title' => $this->title,
                'description' => $this->description,
                'priority' => $this->priority,
                'status' => $this->status,
                'cost' => $this->cost ?: 0,
            ]
        );

        // Jika status perbaikan in_progress, otomatis ubah status kamar menjadi maintenance
        if ($this->status === 'in_progress') {
            Room::where('id', $this->room_id)->update(['status' => 'maintenance']);
        } elseif ($this->status === 'completed') {
            // Jika selesai dan tidak ada kontrak aktif, kembalikan ke available
            $room = Room::find($this->room_id);
            if ($room && $room->status === 'maintenance') {
                $room->update(['status' => 'available']);
            }
        }

        session()->flash('message', $this->requestId ? 'Laporan perbaikan diperbarui.' : 'Laporan perbaikan baru ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $currentTeam = Auth::user()->currentTeam;
        $req = MaintenanceRequest::where('team_id', $currentTeam->id)->findOrFail($id);

        $this->requestId = $id;
        $this->room_id = $req->room_id;
        $this->customer_id = $req->customer_id;
        $this->title = $req->title;
        $this->description = $req->description;
        $this->priority = $req->priority;
        $this->status = $req->status;
        $this->cost = $req->cost;

        $this->isModalOpen = true;
    }

    public function render()
    {
        $currentTeam = Auth::user()->currentTeam;

        $rooms = Room::where('team_id', $currentTeam->id)->orderBy('room_number')->get();
        $tenants = Customer::where('team_id', $currentTeam->id)->orderBy('name')->get();

        $requests = MaintenanceRequest::where('team_id', $currentTeam->id)
            ->with(['room', 'tenant'])
            ->when($this->search, function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhereHas('room', fn($r) => $r->where('room_number', 'like', '%' . $this->search . '%'));
            })
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->priorityFilter, fn($q) => $q->where('priority', $this->priorityFilter))
            ->latest()
            ->paginate(10);

        return view('livewire.maintenance.index', [
            'requests' => $requests,
            'rooms' => $rooms,
            'tenants' => $tenants,
        ])->layout('layouts.app');
    }
}