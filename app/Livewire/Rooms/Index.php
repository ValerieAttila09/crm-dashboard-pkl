<?php

namespace App\Livewire\Rooms;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Room;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $propertyFilter = '';

    // Form Modal Fields
    public $isModalOpen = false;
    public $roomId = null;
    public $property_id, $room_number, $type = 'Studio', $price_per_month = 0, $status = 'available', $panorama_360_url = '';
    public $amenities = []; // Array pilihan fasilitas

    protected $rules = [
        'property_id' => 'required|exists:properties,id',
        'room_number' => 'required|string|max:50',
        'type' => 'required|string|max:50',
        'price_per_month' => 'required|numeric|min:0',
        'status' => 'required|in:available,occupied,maintenance',
        'panorama_360_url' => 'nullable|url',
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
        $this->roomId = null;
        $this->property_id = '';
        $this->room_number = '';
        $this->type = 'Studio';
        $this->price_per_month = 0;
        $this->status = 'available';
        $this->panorama_360_url = '';
        $this->amenities = [];
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();
        $currentTeam = Auth::user()->currentTeam;

        Room::updateOrCreate(
            ['id' => $this->roomId],
            [
                'team_id' => $currentTeam->id,
                'property_id' => $this->property_id,
                'room_number' => $this->room_number,
                'type' => $this->type,
                'price_per_month' => $this->price_per_month,
                'status' => $this->status,
                'panorama_360_url' => $this->panorama_360_url ?: null,
                'amenities' => $this->amenities,
            ]
        );

        session()->flash('message', $this->roomId ? 'Data kamar berhasil diperbarui.' : 'Kamar baru berhasil ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $currentTeam = Auth::user()->currentTeam;
        $room = Room::where('team_id', $currentTeam->id)->findOrFail($id);

        $this->roomId = $id;
        $this->property_id = $room->property_id;
        $this->room_number = $room->room_number;
        $this->type = $room->type;
        $this->price_per_month = $room->price_per_month;
        $this->status = $room->status;
        $this->panorama_360_url = $room->panorama_360_url;
        $this->amenities = $room->amenities ?? [];

        $this->isModalOpen = true;
    }

    public function render()
    {
        $currentTeam = Auth::user()->currentTeam;

        $properties = Property::where('team_id', $currentTeam->id)->orderBy('name')->get();

        $rooms = Room::where('team_id', $currentTeam->id)
            ->with('property')
            ->when($this->search, function ($q) {
                $q->where('room_number', 'like', '%' . $this->search . '%')
                  ->orWhere('type', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->propertyFilter, fn($q) => $q->where('property_id', $this->propertyFilter))
            ->latest()
            ->paginate(12);

        return view('livewire.rooms.index', [
            'rooms' => $rooms,
            'properties' => $properties,
        ])->layout('layouts.app');
    }
}