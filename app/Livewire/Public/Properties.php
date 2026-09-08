<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Room;
use App\Models\Property;

class Properties extends Component
{
    use WithPagination;

    public $search = '';
    public $propertyId = '';
    public $type = '';
    public $maxPrice = 10000000;

    protected $queryString = [
        'search' => ['except' => ''],
        'propertyId' => ['except' => ''],
        'type' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'propertyId', 'type', 'maxPrice']);
        $this->resetPage();
    }

    public function render()
    {
        $properties = Property::orderBy('name')->get();

        $rooms = Room::with(['property', 'scenes'])
            ->where('status', 'available')
            ->when($this->search, function ($q) {
                $q->where('room_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('property', fn($p) => $p->where('name', 'like', '%' . $this->search . '%'));
            })
            ->when($this->propertyId, fn($q) => $q->where('property_id', $this->propertyId))
            ->when($this->type, fn($q) => $q->where('type', $this->type))
            ->when($this->maxPrice, fn($q) => $q->where('price_per_month', '<=', $this->maxPrice))
            ->latest()
            ->paginate(9);

        return view('livewire.public.properties', [
            'rooms' => $rooms,
            'properties' => $properties,
        ])->layout('layouts.guest');
    }
}