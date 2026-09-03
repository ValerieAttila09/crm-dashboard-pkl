<?php

namespace App\Livewire\Rooms;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Room;
use App\Models\RoomScene;
use App\Models\RoomHotspot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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

        if ($this->room->scenes->isEmpty() && $this->room->panorama_360_url) {
            RoomScene::create([
                'room_id' => $this->room->id,
                'title' => 'Scene Utama',
                'image_url' => $this->room->panorama_360_url,
                'is_default' => true,
            ]);
            $this->room->load('scenes.hotspots.targetScene');
        }

        $defaultScene = $this->room->scenes->where('is_default', true)->first() ?? $this->room->scenes->first();
        $this->activeSceneId = $defaultScene ? $defaultScene->id : null;
    }

    public function selectScene($sceneId)
    {
        $this->activeSceneId = $sceneId;
        $this->dispatch('load-scene', scene: $this->viewerSceneData($sceneId));
    }

    private function viewerSceneData($sceneId): array
    {
        $scene = $this->room->scenes->firstWhere('id', $sceneId);

        return [
            'imageUrl' => $scene?->image_url,
            'hotspots' => $scene?->hotspots->map(fn (RoomHotspot $hotspot) => [
                'pitch' => (float) $hotspot->pitch,
                'yaw' => (float) $hotspot->yaw,
                'title' => $hotspot->title,
                'targetSceneId' => (string) $hotspot->target_scene_id,
            ])->values()->all() ?? [],
        ];
    }

    public function openSceneModal()
    {
        $this->reset(['new_scene_title', 'new_scene_image', 'is_default']);
        $this->isSceneModalOpen = true;
    }

    public function storeScene()
    {
        $this->validate();

        $fileName = $this->new_scene_image->hashName();

        // 1. Upload file ke Supabase S3
        Storage::disk('supabase')->put(
            $fileName,
            file_get_contents($this->new_scene_image->getRealPath())
        );

        // 2. Format URL Publik resmi Supabase Storage
        $bucket = env('AWS_BUCKET', 'room-360');
        $imageUrl = "https://rervvjhlozoxojtikygk.supabase.co/storage/v1/object/public/{$bucket}/{$fileName}";

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
        $this->dispatch('load-scene', scene: $this->viewerSceneData($scene->id));

        session()->flash('message', 'Ruangan 360° berhasil diunggah.');
    }

    // Dipanggil via Event JavaScript saat user mengklik viewer 360°
    public function captureHotspotCoords($pitch, $yaw)
    {
        $this->clickedPitch = round($pitch, 2);
        $this->clickedYaw = round($yaw, 2);
        $this->reset(['target_scene_id', 'hotspot_title']);
        $this->isHotspotModalOpen = true;
    }

    public function openHotspotEditor()
    {
        $this->isHotspotModalOpen = true;
        $this->dispatch('open-hotspot-editor', scene: $this->viewerSceneData($this->activeSceneId));
    }

    public function storeHotspot()
    {
        $this->validate([
            'target_scene_id' => [
                'required',
                Rule::exists('room_scenes', 'id')->where(fn ($query) => $query->where('room_id', $this->room->id)),
            ],
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
        $this->dispatch('load-scene', scene: $this->viewerSceneData($this->activeSceneId));

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