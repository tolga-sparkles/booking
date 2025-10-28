<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    // Reservation Management Tests
    public function test_admin_can_access_reservation_management()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('admin.reservations.index'));
        $response->assertStatus(200);
    }

    public function test_manager_can_access_reservation_management()
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $response = $this->actingAs($manager)->get(route('admin.reservations.index'));
        $response->assertStatus(200);
    }

    // User Management Tests
    public function test_admin_can_access_user_management()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('admin.users.index'));
        $response->assertStatus(200);
    }

    public function test_manager_can_access_user_management()
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $response = $this->actingAs($manager)->get(route('admin.users.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_update_any_user_role()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $user), ['role' => 'expert']);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => 'expert']);
    }

    public function test_manager_can_update_non_admin_user_roles()
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($manager)->put(route('admin.users.update', $user), ['role' => 'expert']);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => 'expert']);
    }

    public function test_manager_cannot_update_an_admin_role()
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($manager)->put(route('admin.users.update', $admin), ['role' => 'customer']);

        $response->assertStatus(403);
    }

    public function test_manager_cannot_promote_a_user_to_admin()
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($manager)->put(route('admin.users.update', $user), ['role' => 'admin']);

        $response->assertStatus(403);
    }

    // Authorization Tests
    public function test_non_admin_or_manager_cannot_access_admin_areas()
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($user)->get(route('admin.reservations.index'));
        $response->assertRedirect('/');

        $response = $this->actingAs($user)->get(route('admin.users.index'));
        $response->assertRedirect('/');
    }
}
