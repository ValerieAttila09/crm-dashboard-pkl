<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;

class About extends Component
{
    public function render()
    {
        // Metric data nyata dari database untuk statistik di About Page
        $totalProperties = Property::count();
        $totalRooms = Room::count();
        $totalOccupied = Room::where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($totalOccupied / $totalRooms) * 100) : 0;

        return view('livewire.public.about', [
            'totalProperties' => $totalProperties,
            'totalRooms' => $totalRooms,
            'occupancyRate' => $occupancyRate,
        ])->layout('layouts.guest');
    }
}