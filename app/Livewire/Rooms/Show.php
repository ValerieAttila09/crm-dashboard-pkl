<?php

namespace App\Livewire\Rooms;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Room;
use App\Models\RoomScene;
use App\Models\RoomHotspot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Show extends Component
{
    use WithFileUploads;

    public Room $room;
    public $activeSceneId = null;

    // State Upload Scene Baru
    public $new_scene_title = '';
    public $new_scene_image;
    public $is_default = false;
    public $isSceneModalOpen = false;

    // State Tambah Hotspot Via Klik 360
    public $isHotspotModalOpen = false;
    public $clickedPitch = 0;
    public $clickedYaw = 0;
    public $target_scene_id = '';
    public $hotspot_title = '';

    protected function rules()
    {
        return [
            'new_scene_title' => 'required|string|max:255',
            'new_scene_image' => 'required|image|max:10240',
        ];
    }

    public function mount($id)
    {
        $currentTeam = Auth::user()->currentTeam;
        $this->room = Room::where('team_id', $currentTeam->id)
            ->with(['property', 'scenes.hotspots.targetScene'])
            ->findOrFail($id);

        $defaultScene = $this->room->scenes->where('is_default', true)->first() ?? $this->room->scenes->first();
        $this->activeSceneId = $defaultScene ? $defaultScene->id : null;
    }

    public function selectScene($sceneId)
    {
        $this->activeSceneId = $sceneId;
        $this->dispatch('load-scene', sceneId: $sceneId);
    }

    public function openSceneModal()
    {
        $this->reset(['new_scene_title', 'new_scene_image', 'is_default']);
        $this->isSceneModalOpen = true;
    }

    public function storeScene()
    {
        $this->validate();

        // Upload gambar ke Supabase Storage
        $storedPath = $this->new_scene_image->store('', 'supabase');
        $imageUrl = Storage::disk('supabase')->url($storedPath);

        // Jika diset default, reset scene lain
        if ($this->is_default) {
            RoomScene::where('room_id', $this->room->id)->update(['is_default' => false]);
        }

        $scene = RoomScene::create([
            'room_id' => $this->room->id,
            'title' => $this->new_scene_title,
            'image_url' => $imageUrl,
            'is_default' => $this->is_default || $this->room->scenes()->count() === 0,
        ]);

        $this->isSceneModalOpen = false;
        $this->activeSceneId = $scene->id;
        $this->room->load('scenes.hotspots.targetScene');
        $this->dispatch('load-scene', sceneId: $scene->id);

        session()->flash('message', 'Ruangan 360° baru berhasil ditambahkan.');
    }

    // Dipanggil via Event JavaScript saat user mengklik viewer 360°
    public function captureHotspotCoords($pitch, $yaw)
    {
        $this->clickedPitch = round($pitch, 2);
        $this->clickedYaw = round($yaw, 2);
        $this->reset(['target_scene_id', 'hotspot_title']);
        $this->isHotspotModalOpen = true;
    }

    public function storeHotspot()
    {
        $this->validate([
            'target_scene_id' => 'required|exists:room_scenes,id',
            'hotspot_title' => 'required|string|max:255',
        ]);

        RoomHotspot::create([
            'room_scene_id' => $this->activeSceneId,
            'target_scene_id' => $this->target_scene_id,
            'title' => $this->hotspot_title,
            'pitch' => $this->clickedPitch,
            'yaw' => $this->clickedYaw,
        ]);

        $this->isHotspotModalOpen = false;
        $this->room->load('scenes.hotspots.targetScene');
        $this->dispatch('load-scene', sceneId: $this->activeSceneId);

        session()->flash('message', 'Hotspot navigasi berhasil ditambahkan.');
    }

    public function render()
    {
        $activeScene = RoomScene::with('hotspots.targetScene')->find($this->activeSceneId);

        return view('livewire.rooms.show', [
            'activeScene' => $activeScene,
        ])->layout('layouts.app');
    }
}