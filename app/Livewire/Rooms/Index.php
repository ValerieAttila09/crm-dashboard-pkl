<?php

namespace App\Livewire\Rooms;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Room;
use App\Models\RoomScene;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $statusFilter = '';
    public $propertyFilter = '';
    public $viewMode = 'list';

    // Form Modal Fields
    public $isModalOpen = false;
    public $roomId = null;
    public $property_id, $room_number, $type = 'Studio', $price_per_month = 0, $status = 'available';
    public $panorama_image;
    public $old_panorama_url;

    // Inline Property
    public $isCreatingProperty = false;
    public $new_property_name = '';
    public $new_property_address = '';

    protected function rules()
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'room_number' => 'required|string|max:50',
            'type' => 'required|string|max:50',
            'price_per_month' => 'required|numeric|min:0',
            'status' => 'required|in:available,occupied,maintenance',
            'panorama_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:51200',
        ];
    }

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
        $this->panorama_image = null;
        $this->old_panorama_url = null;
        $this->isCreatingProperty = false;
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();
        $currentTeam = Auth::user()->currentTeam;

        $imagePath = $this->old_panorama_url;

        if ($this->panorama_image) {
            // 1. Hapus file lama jika ada
            if ($this->old_panorama_url) {
                $oldFileName = basename(parse_url($this->old_panorama_url, PHP_URL_PATH));
                Storage::disk('supabase')->delete($oldFileName);
            }

            // 2. Ambil nama file unik
            $fileName = $this->panorama_image->hashName();

            // 3. Upload konten file langsung (bebas error SignatureDoesNotMatch)
            Storage::disk('supabase')->put(
                $fileName,
                file_get_contents($this->panorama_image->getRealPath())
            );

            // 4. Ambil URL Publik Supabase
            $imagePath = Storage::disk('supabase')->url($fileName);
        }

        $isNewRoom = !$this->roomId;
        $room = Room::updateOrCreate(
            ['id' => $this->roomId],
            [
                'team_id' => $currentTeam->id,
                'property_id' => $this->property_id,
                'room_number' => $this->room_number,
                'type' => $this->type,
                'price_per_month' => $this->price_per_month,
                'status' => $this->status,
                'panorama_360_url' => $imagePath,
            ]
        );

        if ($imagePath && ($isNewRoom || !$room->scenes()->exists())) {
            RoomScene::create([
                'room_id' => $room->id,
                'title' => 'Scene Utama',
                'image_url' => $imagePath,
                'is_default' => true,
            ]);
        }

        session()->flash('message', 'Kamar berhasil disimpan.');
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
        $this->old_panorama_url = $room->panorama_360_url;

        $this->isModalOpen = true;
    }

    public function toggleCreateProperty()
    {
        $this->isCreatingProperty = !$this->isCreatingProperty;
    }

    public function storeProperty()
    {
        $this->validate([
            'new_property_name' => 'required|string|max:255',
        ]);

        $property = Property::create([
            'team_id' => Auth::user()->currentTeam->id,
            'name' => $this->new_property_name,
            'address' => $this->new_property_address,
        ]);

        $this->property_id = $property->id;
        $this->isCreatingProperty = false;
        $this->new_property_name = '';
        $this->new_property_address = '';
    }

    public function setViewMode($mode)
    {
        if (in_array($mode, ['grid', 'list'])) {
            $this->viewMode = $mode;
        }
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