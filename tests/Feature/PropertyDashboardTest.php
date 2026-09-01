<?php

namespace Tests\Feature;

use App\Enums\TeamRole;
use App\Livewire\CrmDashboard;
use App\Models\Customer;
use App\Models\Lease;
use App\Models\Property;
use App\Models\Room;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PropertyDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function makeAuthenticatedUserForTeam(Team $team): User
    {
        $user = User::factory()->create();
        $user->teams()->attach($team->id, ['role' => TeamRole::Owner->value]);
        $user->switchTeam($team);

        return $user;
    }

    public function test_occupancy_rate_is_calculated_correctly(): void
    {
        $team = Team::factory()->create();
        $user = $this->makeAuthenticatedUserForTeam($team);
        $property = Property::create([
            'team_id' => $team->id,
            'name' => 'Skyline Property',
        ]);

        Room::create([
            'team_id' => $team->id,
            'property_id' => $property->id,
            'room_number' => '101',
            'type' => 'Studio',
            'price_per_month' => 2000000,
            'status' => 'occupied',
        ]);

        Room::create([
            'team_id' => $team->id,
            'property_id' => $property->id,
            'room_number' => '102',
            'type' => 'Studio',
            'price_per_month' => 2200000,
            'status' => 'occupied',
        ]);

        Room::create([
            'team_id' => $team->id,
            'property_id' => $property->id,
            'room_number' => '103',
            'type' => '1BR',
            'price_per_month' => 3000000,
            'status' => 'available',
        ]);

        Livewire::actingAs($user)
            ->test(CrmDashboard::class)
            ->assertSet('occupancyRate', 66.7);
    }

    public function test_monthly_revenue_only_counts_paid_active_leases(): void
    {
        $team = Team::factory()->create();
        $user = $this->makeAuthenticatedUserForTeam($team);
        $property = Property::create([
            'team_id' => $team->id,
            'name' => 'Harbor View',
        ]);

        $roomA = Room::create([
            'team_id' => $team->id,
            'property_id' => $property->id,
            'room_number' => '201',
            'type' => 'Studio',
            'price_per_month' => 2500000,
            'status' => 'occupied',
        ]);

        $roomB = Room::create([
            'team_id' => $team->id,
            'property_id' => $property->id,
            'room_number' => '202',
            'type' => '1BR',
            'price_per_month' => 3600000,
            'status' => 'occupied',
        ]);

        $tenantA = Customer::create([
            'team_id' => $team->id,
            'name' => 'Ari Setiawan',
            'email' => 'ari@example.com',
            'phone' => '081122334455',
            'status' => 'active',
        ]);

        $tenantB = Customer::create([
            'team_id' => $team->id,
            'name' => 'Maya Putri',
            'email' => 'maya@example.com',
            'phone' => '081133344556',
            'status' => 'active',
        ]);

        Lease::create([
            'team_id' => $team->id,
            'room_id' => $roomA->id,
            'customer_id' => $tenantA->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'monthly_rent' => 2500000,
            'payment_status' => 'paid',
            'status' => 'active',
        ]);

        Lease::create([
            'team_id' => $team->id,
            'room_id' => $roomB->id,
            'customer_id' => $tenantB->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'monthly_rent' => 3600000,
            'payment_status' => 'unpaid',
            'status' => 'active',
        ]);

        Livewire::actingAs($user)
            ->test(CrmDashboard::class)
            ->assertSet('monthlyRevenueTarget', 2500000.0);
    }
}
