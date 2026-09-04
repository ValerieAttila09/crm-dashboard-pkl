<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Room;
use App\Models\Property;
use App\Models\RoomScene;
use Illuminate\Support\Facades\Auth;

class GlobalSearch extends Component
{
    public $search = '';
    public $isOpen = false;
    public $selectedIndex = 0;

    public function updatedSearch()
    {
        $this->selectedIndex = 0;
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->selectedIndex = 0;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->search = '';
        $this->selectedIndex = 0;
    }

    public function render()
    {
        $results = [
            'rooms' => collect(),
            'properties' => collect(),
            'scenes' => collect(),
        ];

        // Sanitisasi query string
        $query = trim(preg_replace('/\s+/', ' ', $this->search));

        if (strlen($query) >= 2) {
            $teamId = Auth::user()->currentTeam->id;

            // 1. Cari Kamar / Unit
            $results['rooms'] = Room::where('team_id', $teamId)
                ->with('property')
                ->where(function ($q) use ($query) {
                    $q->where('room_number', 'ilike', '%' . $query . '%')
                      ->orWhere('type', 'ilike', '%' . $query . '%');
                })
                ->take(5)
                ->get();

            // 2. Cari Properti / Gedung
            $results['properties'] = Property::where('team_id', $teamId)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'ilike', '%' . $query . '%')
                      ->orWhere('address', 'ilike', '%' . $query . '%');
                })
                ->take(5)
                ->get();

            // 3. Cari Scene 360° / Ruangan
            $results['scenes'] = RoomScene::whereHas('room', function ($q) use ($teamId) {
                    $q->where('team_id', $teamId);
                })
                ->with('room')
                ->where('title', 'ilike', '%' . $query . '%')
                ->take(5)
                ->get();
        }

        return view('livewire.global-search', [
            'results' => $results,
        ]);
    }
}