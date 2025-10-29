<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReservationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    public function test_admin_user_can_access_admin_panel()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.reservations.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.reservations.index');
    }

    public function test_manager_user_can_access_admin_panel()
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $response = $this->actingAs($manager)->get(route('admin.reservations.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.reservations.index');
    }

    public function test_admin_can_delete_any_reservation()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $reservation = Reservation::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.reservations.destroy', $reservation));

        $response->assertRedirect(route('admin.reservations.index'));
        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
    }
}
