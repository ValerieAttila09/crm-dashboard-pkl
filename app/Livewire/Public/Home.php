<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\Room;
use App\Models\Property;

class Home extends Component
{
    public $searchLocation = '';
    public $selectedType = '';

    public function render()
    {
        // 1. Ambil Properti Unggulan
        $featuredProperties = Property::withCount('rooms')
            ->take(3)
            ->get();

        // 2. Ambil Unit Kamar yang Berstatus Available & Memiliki Virtual Tour 360°
        $featuredRooms = Room::with(['property', 'scenes'])
            ->where('status', 'available')
            ->when($this->searchLocation, function ($q) {
                $q->whereHas('property', function ($pQuery) {
                    $pQuery->where('name', 'like', '%' . $this->searchLocation . '%')
                           ->orWhere('address', 'like', '%' . $this->searchLocation . '%');
                });
            })
            ->when($this->selectedType, fn($q) => $q->where('type', $this->selectedType))
            ->take(6)
            ->get();

        return view('livewire.public.home', [
            'featuredProperties' => $featuredProperties,
            'featuredRooms' => $featuredRooms,
        ])->layout('layouts.guest');
    }
}