<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_delete_their_own_reservation()
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('reservations.destroy', $reservation));

        $response->assertRedirect(route('reservations.index'));
        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
    }

    public function test_user_cannot_delete_another_users_reservation()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $reservation = Reservation::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete(route('reservations.destroy', $reservation));

        $response->assertStatus(403);
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);
    }

    public function test_user_can_view_edit_page_for_their_own_reservation()
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('reservations.edit', $reservation));

        $response->assertStatus(200);
        $response->assertViewIs('reservations.edit');
    }

    public function test_user_cannot_view_edit_page_for_another_users_reservation()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $reservation = Reservation::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(route('reservations.edit', $reservation));

        $response->assertStatus(403);
    }

    public function test_user_can_update_their_own_reservation()
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create(['user_id' => $user->id]);

        $newStartTime = now()->addHours(1)->format('Y-m-d\TH:i');
        $newEndTime = now()->addHours(2)->format('Y-m-d\TH:i');

        $response = $this->actingAs($user)->put(route('reservations.update', $reservation), [
            'start_time' => $newStartTime,
            'end_time' => $newEndTime,
        ]);

        $response->assertRedirect(route('reservations.index'));
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'start_time' => $newStartTime,
            'end_time' => $newEndTime,
        ]);
    }

    public function test_user_cannot_update_another_users_reservation()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $reservation = Reservation::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->put(route('reservations.update', $reservation), [
            'start_time' => now()->addHours(1)->format('Y-m-d\TH:i'),
            'end_time' => now()->addHours(2)->format('Y-m-d\TH:i'),
        ]);

        $response->assertStatus(403);
    }

    public function test_user_cannot_update_reservation_to_overlap_with_another()
    {
        $user = User::factory()->create();
        $existingReservation = Reservation::factory()->create([
            'start_time' => now()->addHours(2),
            'end_time' => now()->addHours(3),
        ]);
        $reservationToUpdate = Reservation::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('reservations.update', $reservationToUpdate), [
            'start_time' => now()->addHours(2)->format('Y-m-d\TH:i'),
            'end_time' => now()->addHours(3)->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHasErrors('start_time');
    }
}
