<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReservationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_user_cannot_access_admin_panel()
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get(route('admin.reservations.index'));

        $response->assertRedirect('/');
    }

    public function test_admin_user_can_access_admin_panel()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get(route('admin.reservations.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.reservations.index');
    }
}
