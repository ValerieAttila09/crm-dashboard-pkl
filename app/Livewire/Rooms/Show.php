<?php

namespace App\Livewire\Rooms;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Room;
use App\Models\RoomScene;
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
            ->with(['property', 'scenes'])
            ->findOrFail($id);

        if ($this->room->scenes->isEmpty() && $this->room->panorama_360_url) {
            RoomScene::create([
                'room_id' => $this->room->id,
                'title' => 'Scene Utama',
                'image_url' => $this->room->panorama_360_url,
                'is_default' => true,
            ]);
            $this->room->load('scenes');
        }

        $defaultScene = $this->room->scenes->where('is_default', true)->first() ?? $this->room->scenes->first();
        $this->activeSceneId = $defaultScene ? $defaultScene->id : null;
    }

    public function selectScene($sceneId)
    {
        $scene = $this->room->scenes->firstWhere('id', $sceneId);
        if (!$scene) {
            return;
        }

        $this->activeSceneId = $sceneId;
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
        $this->room->load('scenes');

        session()->flash('message', 'Ruangan 360° berhasil diunggah.');
    }


    public function render()
    {
        $activeScene = $this->room->scenes->firstWhere('id', $this->activeSceneId);

        return view('livewire.rooms.show', [
            'activeScene' => $activeScene,
        ])->layout('layouts.app');
    }
}