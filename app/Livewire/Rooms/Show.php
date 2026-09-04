<?php

namespace App\Livewire\Rooms;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Room;
use App\Models\RoomScene;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function mount($roomNumber)
    {
        $currentTeam = Auth::user()->currentTeam;
        $this->room = Room::where('team_id', $currentTeam->id)
            ->with(['property', 'scenes'])
            ->where(function ($query) use ($roomNumber) {
                $query->where('room_number', $roomNumber);

                if (Str::isUuid($roomNumber)) {
                    $query->orWhere('id', $roomNumber);
                }
            })
            ->firstOrFail();

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
        $requestedScene = request()->query('scene');
        $selectedScene = $requestedScene
            ? $this->room->scenes->firstWhere('id', $requestedScene)
            : null;
        $this->activeSceneId = ($selectedScene ?? $defaultScene)?->id;
    }

    public function selectScene($sceneId)
    {
        $scene = $this->room->scenes->firstWhere('id', $sceneId);
        if (!$scene) {
            return;
        }

        $this->activeSceneId = $sceneId;
        return $this->redirect(route('rooms.show', [
            'current_team' => Auth::user()->currentTeam->slug,
            'roomNumber' => $this->room->room_number,
            'scene' => $scene->id,
        ]), navigate: false);
    }

    private function viewerTourData(): array
    {
        return $this->room->scenes->mapWithKeys(function (RoomScene $scene) {

            return [(string) $scene->id => [
                'title' => $scene->title,
                'imageUrl' => $scene->image_url,
            ]];
        })->all();
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

        $this->redirect(route('rooms.show', [
            'current_team' => Auth::user()->currentTeam->slug,
            'roomNumber' => $this->room->room_number,
            'scene' => $scene->id,
        ]), navigate: false);

        session()->flash('message', 'Ruangan 360° berhasil diunggah.');
    }


    public function render()
    {
        $activeScene = RoomScene::find($this->activeSceneId);
        $tourData = $this->viewerTourData();

        return view('livewire.rooms.show', [
            'activeScene' => $activeScene,
            'tourData' => $tourData,
        ])->layout('layouts.app');
    }
}