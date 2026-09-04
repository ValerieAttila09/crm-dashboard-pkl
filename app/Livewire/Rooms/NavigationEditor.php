<?php

namespace App\Livewire\Rooms;

use App\Models\Room;
use App\Models\RoomHotspot;
use App\Models\RoomScene;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class NavigationEditor extends Component
{
    public Room $room;
    public RoomScene $activeScene;
    public bool $isHotspotModalOpen = false;
    public ?string $editingHotspotId = null;
    public float $pitch = 0;
    public float $yaw = 0;
    public string $hotspotTitle = '';
    public string $hotspotLabel = '';
    public string $hotspotDescription = '';
    public string $targetSceneId = '';

    public function mount(string $roomNumber, string $scene): void
    {
        $team = Auth::user()->currentTeam;
        $this->room = Room::where('team_id', $team->id)
            ->where(fn ($query) => $query->where('room_number', $roomNumber)
                ->when(Str::isUuid($roomNumber), fn ($query) => $query->orWhere('id', $roomNumber)))
            ->with('scenes.hotspots')
            ->firstOrFail();
        $this->activeScene = $this->room->scenes->firstWhere('id', $scene) ?? abort(404);
    }

    public function openHotspotModal(float $pitch, float $yaw): void
    {
        $this->resetValidation();
        $this->reset(['editingHotspotId', 'hotspotTitle', 'hotspotLabel', 'hotspotDescription', 'targetSceneId']);
        $this->pitch = round($pitch, 2);
        $this->yaw = round($yaw, 2);
        $this->isHotspotModalOpen = true;
    }

    public function storeHotspot(): void
    {
        $this->validate([
            'hotspotTitle' => 'required|string|max:255',
            'hotspotLabel' => 'required|string|max:255',
            'hotspotDescription' => 'nullable|string|max:2000',
            'targetSceneId' => [
                'required',
                Rule::exists('room_scenes', 'id')->where(fn ($query) => $query->where('room_id', $this->room->id)),
            ],
        ]);

        $data = [
                'room_scene_id' => $this->activeScene->id,
                'target_scene_id' => $this->targetSceneId,
                'title' => $this->hotspotTitle,
                'label' => $this->hotspotLabel,
                'description' => $this->hotspotDescription,
                'pitch' => $this->pitch,
                'yaw' => $this->yaw,
            ];

        if ($this->editingHotspotId) {
            RoomHotspot::whereKey($this->editingHotspotId)->update($data);
        } else {
            RoomHotspot::create($data);
        }

        $this->redirect(route('rooms.navigation.edit', [
            'current_team' => Auth::user()->currentTeam->slug,
            'roomNumber' => $this->room->room_number,
            'scene' => $this->activeScene->id,
        ]), navigate: false);
    }

    public function render()
    {
        return view('livewire.rooms.navigation-editor', [
            'scenes' => $this->room->scenes,
            'hotspots' => $this->activeScene->hotspots,
        ])->layout('layouts.app');
    }
}
