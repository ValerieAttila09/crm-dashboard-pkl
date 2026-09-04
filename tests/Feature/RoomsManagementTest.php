<?php

namespace Tests\Feature;

use App\Enums\TeamRole;
use App\Livewire\Rooms\Index as RoomsIndex;
use App\Livewire\Rooms\NavigationEditor;
use App\Livewire\Rooms\Show as RoomShow;
use App\Models\Customer;
use App\Models\Property;
use App\Models\Room;
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

    public function test_switching_scene_selects_the_requested_room(): void
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
        Livewire::actingAs($user)
            ->test(RoomShow::class, ['roomNumber' => $room->room_number])
            ->assertSee((string) $room->id)
            ->assertSee('Ruang Tamu')
            ->assertSee('Dapur')
            ->call('selectScene', $secondScene->id)
            ->assertRedirect(route('rooms.show', [
                'current_team' => $team->slug,
                'roomNumber' => $room->room_number,
                'scene' => $secondScene->id,
            ]));
    }

    public function test_navigation_editor_stores_hotspot_metadata(): void
    {
        $team = Team::factory()->create();
        $user = $this->makeAuthenticatedUserForTeam($team);
        $property = Property::create(['team_id' => $team->id, 'name' => 'Oak Terrace']);
        $room = Room::create([
            'team_id' => $team->id,
            'property_id' => $property->id,
            'room_number' => 'E-404',
            'type' => 'Studio',
            'price_per_month' => 2500000,
            'status' => 'available',
        ]);
        $sourceScene = RoomScene::create(['room_id' => $room->id, 'title' => 'Tamu', 'image_url' => 'https://example.test/tamu.jpg', 'is_default' => true]);
        $targetScene = RoomScene::create(['room_id' => $room->id, 'title' => 'Dapur', 'image_url' => 'https://example.test/dapur.jpg']);

        Livewire::actingAs($user)
            ->test(NavigationEditor::class, ['roomNumber' => $room->room_number, 'scene' => $sourceScene->id])
            ->set('pitch', 12.5)
            ->set('yaw', -30.25)
            ->set('hotspotTitle', 'Ke Dapur')
            ->set('hotspotLabel', 'Dapur')
            ->set('hotspotDescription', 'Buka area dapur')
            ->set('targetSceneId', $targetScene->id)
            ->call('storeHotspot')
            ->assertRedirect();

        $this->assertDatabaseHas('room_hotspots', [
            'room_scene_id' => $sourceScene->id,
            'target_scene_id' => $targetScene->id,
            'label' => 'Dapur',
            'description' => 'Buka area dapur',
        ]);
    }
}
