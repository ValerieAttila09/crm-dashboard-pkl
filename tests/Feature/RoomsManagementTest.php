<?php

namespace Tests\Feature;

use App\Enums\TeamRole;
use App\Livewire\Rooms\Index as RoomsIndex;
use App\Livewire\Rooms\Show as RoomShow;
use App\Models\Customer;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomHotspot;
use App\Models\RoomScene;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class RoomsManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function makeAuthenticatedUserForTeam(Team $team): User
    {
        $user = User::factory()->create();
        $user->teams()->attach($team->id, ['role' => TeamRole::Owner->value]);
        $user->switchTeam($team);

        return $user;
    }

    public function test_admin_can_create_a_new_room(): void
    {
        Storage::fake('supabase');

        $team = Team::factory()->create();
        $user = $this->makeAuthenticatedUserForTeam($team);
        $property = Property::create([
            'team_id' => $team->id,
            'name' => 'Sunset Residence',
            'address' => 'Jl. Melati No. 12',
        ]);

        $uploadedFile = UploadedFile::fake()->image('panorama.jpg', 1200, 800);

        Livewire::actingAs($user)
            ->test(RoomsIndex::class)
            ->set('property_id', $property->id)
            ->set('room_number', 'A-101')
            ->set('type', 'Studio')
            ->set('price_per_month', 2500000)
            ->set('status', 'available')
            ->set('panorama_image', $uploadedFile)
            ->call('store')
            ->assertHasNoErrors();

        $room = Room::query()->where('room_number', 'A-101')->first();

        $this->assertNotNull($room);
        $this->assertNotNull($room->panorama_360_url);
        $this->assertDatabaseHas('room_scenes', [
            'room_id' => $room->id,
            'image_url' => $room->panorama_360_url,
            'is_default' => true,
        ]);
        $this->assertDatabaseHas('rooms', [
            'team_id' => $team->id,
            'property_id' => $property->id,
            'room_number' => 'A-101',
            'status' => 'available',
            'price_per_month' => '2500000.00',
        ]);
    }

    public function test_room_data_is_isolated_per_team(): void
    {
        $teamA = Team::factory()->create(['name' => 'Team A']);
        $teamB = Team::factory()->create(['name' => 'Team B']);

        $user = User::factory()->create();
        $user->teams()->attach($teamA->id, ['role' => TeamRole::Owner->value]);
        $user->teams()->attach($teamB->id, ['role' => TeamRole::Owner->value]);
        $user->switchTeam($teamA);

        $propertyA = Property::create([
            'team_id' => $teamA->id,
            'name' => 'Property A',
        ]);
        $propertyB = Property::create([
            'team_id' => $teamB->id,
            'name' => 'Property B',
        ]);

        Room::create([
            'team_id' => $teamA->id,
            'property_id' => $propertyA->id,
            'room_number' => 'A-101',
            'type' => 'Studio',
            'price_per_month' => 1800000,
            'status' => 'available',
        ]);

        Room::create([
            'team_id' => $teamB->id,
            'property_id' => $propertyB->id,
            'room_number' => 'B-202',
            'type' => '1BR',
            'price_per_month' => 3200000,
            'status' => 'occupied',
        ]);

        Livewire::actingAs($user)
            ->test(RoomsIndex::class)
            ->assertSee('A-101')
            ->assertDontSee('B-202');

        $this->assertEquals(1, Room::where('team_id', $teamA->id)->count());
        $this->assertEquals(1, Room::where('team_id', $teamB->id)->count());
    }

    public function test_status_validation_only_accepts_allowed_values(): void
    {
        $team = Team::factory()->create();
        $user = $this->makeAuthenticatedUserForTeam($team);
        $property = Property::create([
            'team_id' => $team->id,
            'name' => 'Oak Terrace',
        ]);

        Livewire::actingAs($user)
            ->test(RoomsIndex::class)
            ->set('property_id', $property->id)
            ->set('room_number', 'C-303')
            ->set('type', 'Deluxe')
            ->set('price_per_month', 4200000)
            ->set('status', 'reserved')
            ->call('store')
            ->assertHasErrors(['status']);

        $this->assertDatabaseMissing('rooms', [
            'team_id' => $team->id,
            'room_number' => 'C-303',
        ]);
    }

    public function test_switching_scene_dispatches_the_selected_panorama_and_hotspots(): void
    {
        $team = Team::factory()->create();
        $user = $this->makeAuthenticatedUserForTeam($team);
        $property = Property::create(['team_id' => $team->id, 'name' => 'Oak Terrace']);
        $room = Room::create([
            'team_id' => $team->id,
            'property_id' => $property->id,
            'room_number' => 'D-102',
            'type' => 'Studio',
            'price_per_month' => 2500000,
            'status' => 'available',
        ]);
        $firstScene = RoomScene::create([
            'room_id' => $room->id,
            'title' => 'Ruang Tamu',
            'image_url' => 'https://example.test/living-room.jpg',
            'is_default' => true,
        ]);
        $secondScene = RoomScene::create([
            'room_id' => $room->id,
            'title' => 'Dapur',
            'image_url' => 'https://example.test/kitchen.jpg',
        ]);
        RoomHotspot::create([
            'room_scene_id' => $secondScene->id,
            'target_scene_id' => $firstScene->id,
            'title' => 'Kembali ke Ruang Tamu',
            'pitch' => 10,
            'yaw' => -20,
        ]);

        Livewire::actingAs($user)
            ->test(RoomShow::class, ['id' => $room->id])
            ->call('selectScene', $secondScene->id)
            ->assertDispatched('load-scene', function ($eventName, $params) use ($secondScene, $firstScene) {
                return $eventName === 'load-scene'
                    && $params['scene']['imageUrl'] === $secondScene->image_url
                    && $params['scene']['hotspots'][0]['targetSceneId'] === $firstScene->id;
            });
    }

    public function test_storing_hotspot_persists_it_for_the_active_scene(): void
    {
        $team = Team::factory()->create();
        $user = $this->makeAuthenticatedUserForTeam($team);
        $property = Property::create(['team_id' => $team->id, 'name' => 'Oak Terrace']);
        $room = Room::create([
            'team_id' => $team->id,
            'property_id' => $property->id,
            'room_number' => 'D-102',
            'type' => 'Studio',
            'price_per_month' => 2500000,
            'status' => 'available',
        ]);
        $sourceScene = RoomScene::create([
            'room_id' => $room->id,
            'title' => 'Ruang Tamu',
            'image_url' => 'https://example.test/living-room.jpg',
            'is_default' => true,
        ]);
        $targetScene = RoomScene::create([
            'room_id' => $room->id,
            'title' => 'Dapur',
            'image_url' => 'https://example.test/kitchen.jpg',
        ]);

        Livewire::actingAs($user)
            ->test(RoomShow::class, ['id' => $room->id])
            ->set('clickedPitch', 10.25)
            ->set('clickedYaw', -20.5)
            ->set('activeSceneId', $sourceScene->id)
            ->set('target_scene_id', $targetScene->id)
            ->set('hotspot_title', 'Masuk ke Dapur')
            ->call('storeHotspot')
            ->assertHasNoErrors()
            ->assertDispatched('load-scene');

        $this->assertDatabaseHas('room_hotspots', [
            'room_scene_id' => $sourceScene->id,
            'target_scene_id' => $targetScene->id,
            'title' => 'Masuk ke Dapur',
        ]);
    }
}
