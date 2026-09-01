<?php

namespace Tests\Feature;

use App\Enums\TeamRole;
use App\Livewire\Leases\Index as LeasesIndex;
use App\Models\Customer;
use App\Models\Lease;
use App\Models\Property;
use App\Models\Room;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeasesManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function makeAuthenticatedUserForTeam(Team $team): User
    {
        $user = User::factory()->create();
        $user->teams()->attach($team->id, ['role' => TeamRole::Owner->value]);
        $user->switchTeam($team);

        return $user;
    }

    public function test_admin_can_create_a_lease_contract(): void
    {
        $team = Team::factory()->create();
        $user = $this->makeAuthenticatedUserForTeam($team);

        $property = Property::create([
            'team_id' => $team->id,
            'name' => 'Aster Residence',
        ]);

        $room = Room::create([
            'team_id' => $team->id,
            'property_id' => $property->id,
            'room_number' => 'A-105',
            'type' => 'Studio',
            'price_per_month' => 2500000,
            'status' => 'available',
        ]);

        $tenant = Customer::create([
            'team_id' => $team->id,
            'name' => 'Dewi Lestari',
            'email' => 'dewi@example.com',
            'phone' => '08123456789',
            'company' => 'PT Example',
            'status' => 'active',
        ]);

        Livewire::actingAs($user)
            ->test(LeasesIndex::class)
            ->set('room_id', $room->id)
            ->set('customer_id', $tenant->id)
            ->set('start_date', '2026-01-01')
            ->set('end_date', '2026-12-31')
            ->set('monthly_rent', 2500000)
            ->set('payment_status', 'unpaid')
            ->set('status', 'active')
            ->call('store')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('leases', [
            'team_id' => $team->id,
            'room_id' => $room->id,
            'customer_id' => $tenant->id,
            'payment_status' => 'unpaid',
            'status' => 'active',
        ]);
    }

    public function test_active_lease_updates_room_status_to_occupied(): void
    {
        $team = Team::factory()->create();
        $user = $this->makeAuthenticatedUserForTeam($team);

        $property = Property::create([
            'team_id' => $team->id,
            'name' => 'Palm Heights',
        ]);

        $room = Room::create([
            'team_id' => $team->id,
            'property_id' => $property->id,
            'room_number' => 'B-201',
            'type' => '1BR',
            'price_per_month' => 3200000,
            'status' => 'available',
        ]);

        $tenant = Customer::create([
            'team_id' => $team->id,
            'name' => 'Raka Wibisono',
            'email' => 'raka@example.com',
            'phone' => '08111222333',
            'status' => 'active',
        ]);

        Livewire::actingAs($user)
            ->test(LeasesIndex::class)
            ->set('room_id', $room->id)
            ->set('customer_id', $tenant->id)
            ->set('start_date', '2026-02-01')
            ->set('end_date', '2026-08-31')
            ->set('monthly_rent', 3200000)
            ->set('payment_status', 'paid')
            ->set('status', 'active')
            ->call('store');

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'status' => 'occupied',
        ]);
    }

    public function test_payment_status_can_be_updated_for_existing_lease(): void
    {
        $team = Team::factory()->create();
        $user = $this->makeAuthenticatedUserForTeam($team);

        $property = Property::create([
            'team_id' => $team->id,
            'name' => 'Horizon Suites',
        ]);

        $room = Room::create([
            'team_id' => $team->id,
            'property_id' => $property->id,
            'room_number' => 'C-102',
            'type' => 'Deluxe',
            'price_per_month' => 4700000,
            'status' => 'occupied',
        ]);

        $tenant = Customer::create([
            'team_id' => $team->id,
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@example.com',
            'phone' => '08199887766',
            'status' => 'active',
        ]);

        $lease = Lease::create([
            'team_id' => $team->id,
            'room_id' => $room->id,
            'customer_id' => $tenant->id,
            'start_date' => '2026-03-01',
            'end_date' => '2026-12-31',
            'monthly_rent' => 4700000,
            'payment_status' => 'unpaid',
            'status' => 'active',
        ]);

        Livewire::actingAs($user)
            ->test(LeasesIndex::class)
            ->set('leaseId', $lease->id)
            ->set('room_id', $room->id)
            ->set('customer_id', $tenant->id)
            ->set('start_date', '2026-03-01')
            ->set('end_date', '2026-12-31')
            ->set('monthly_rent', 4700000)
            ->set('payment_status', 'paid')
            ->set('status', 'active')
            ->call('store')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('leases', [
            'id' => $lease->id,
            'payment_status' => 'paid',
        ]);
    }
}
