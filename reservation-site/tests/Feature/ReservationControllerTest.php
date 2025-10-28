<?php

namespace Tests\Feature;

use App\Mail\ReservationCancelled;
use App\Mail\ReservationCreated;
use App\Mail\ReservationUpdated;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReservationControllerTest extends TestCase
{
    use RefreshDatabase;

    // Customer Tests
    public function test_customer_can_create_a_reservation()
    {
        Mail::fake();
        $customer = User::factory()->create(['role' => 'customer']);
        $expert = User::factory()->create(['role' => 'expert']);

        $reservationData = [
            'expert_id' => $expert->id,
            'start_time' => now()->addHours(1)->format('Y-m-d\TH:i'),
            'end_time' => now()->addHours(2)->format('Y-m-d\TH:i'),
        ];

        $response = $this->actingAs($customer)->post(route('reservations.store'), $reservationData);

        $response->assertRedirect(route('reservations.index'));
        $this->assertDatabaseHas('reservations', ['expert_id' => $expert->id]);
        Mail::assertSent(ReservationCreated::class, 2); // To customer and expert
    }

    public function test_customer_can_update_their_own_reservation()
    {
        Mail::fake();
        $customer = User::factory()->create(['role' => 'customer']);
        $expert = User::factory()->create(['role' => 'expert']);
        $reservation = Reservation::factory()->create(['user_id' => $customer->id, 'expert_id' => $expert->id]);

        $updateData = [
            'expert_id' => $expert->id,
            'start_time' => now()->addHours(3)->format('Y-m-d\TH:i'),
            'end_time' => now()->addHours(4)->format('Y-m-d\TH:i'),
        ];

        $response = $this->actingAs($customer)->put(route('reservations.update', $reservation), $updateData);

        $response->assertRedirect(route('reservations.index'));
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id, 'start_time' => $updateData['start_time']]);
        Mail::assertSent(ReservationUpdated::class, 2);
    }

    public function test_customer_can_delete_their_own_reservation()
    {
        Mail::fake();
        $customer = User::factory()->create(['role' => 'customer']);
        $expert = User::factory()->create(['role' => 'expert']);
        $reservation = Reservation::factory()->create(['user_id' => $customer->id, 'expert_id' => $expert->id]);

        $response = $this->actingAs($customer)->delete(route('reservations.destroy', $reservation));

        $response->assertRedirect(route('reservations.index'));
        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
        Mail::assertSent(ReservationCancelled::class, 2);
    }

    public function test_customer_can_view_their_own_reservation_details()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $reservation = Reservation::factory()->create(['user_id' => $customer->id]);

        $response = $this->actingAs($customer)->get(route('reservations.show', $reservation));

        $response->assertStatus(200);
        $response->assertViewIs('reservations.show');
    }

    // Expert Tests
    public function test_expert_can_update_a_reservation_assigned_to_them()
    {
        Mail::fake();
        $customer = User::factory()->create(['role' => 'customer']);
        $expert = User::factory()->create(['role' => 'expert']);
        $reservation = Reservation::factory()->create(['user_id' => $customer->id, 'expert_id' => $expert->id]);

        $updateData = [
            'expert_id' => $expert->id,
            'start_time' => now()->addHours(5)->format('Y-m-d\TH:i'),
            'end_time' => now()->addHours(6)->format('Y-m-d\TH:i'),
        ];

        $response = $this->actingAs($expert)->put(route('reservations.update', $reservation), $updateData);

        $response->assertRedirect(route('reservations.index'));
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id, 'start_time' => $updateData['start_time']]);
        Mail::assertSent(ReservationUpdated::class, 2);
    }

    public function test_expert_can_view_a_reservation_assigned_to_them()
    {
        $expert = User::factory()->create(['role' => 'expert']);
        $reservation = Reservation::factory()->create(['expert_id' => $expert->id]);

        $response = $this->actingAs($expert)->get(route('reservations.show', $reservation));

        $response->assertStatus(200);
        $response->assertViewIs('reservations.show');
    }

    // Authorization Tests
    public function test_expert_cannot_create_a_reservation()
    {
        $expert = User::factory()->create(['role' => 'expert']);
        $response = $this->actingAs($expert)->get(route('reservations.create'));
        $response->assertStatus(403);
    }

    public function test_customer_cannot_delete_another_users_reservation()
    {
        $customer1 = User::factory()->create(['role' => 'customer']);
        $customer2 = User::factory()->create(['role' => 'customer']);
        $reservation = Reservation::factory()->create(['user_id' => $customer2->id]);

        $response = $this->actingAs($customer1)->delete(route('reservations.destroy', $reservation));
        $response->assertStatus(403);
    }

    public function test_customer_cannot_view_another_users_reservation_details()
    {
        $customer1 = User::factory()->create(['role' => 'customer']);
        $customer2 = User::factory()->create(['role' => 'customer']);
        $reservation = Reservation::factory()->create(['user_id' => $customer2->id]);

        $response = $this->actingAs($customer1)->get(route('reservations.show', $reservation));
        $response->assertStatus(403);
    }

    // Overlap Test
    public function test_cannot_create_overlapping_reservation_for_same_expert()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $expert = User::factory()->create(['role' => 'expert']);

        Reservation::factory()->create([
            'expert_id' => $expert->id,
            'start_time' => now()->addHours(1),
            'end_time' => now()->addHours(2),
        ]);

        $overlappingData = [
            'expert_id' => $expert->id,
            'start_time' => now()->addMinutes(90)->format('Y-m-d\TH:i'),
            'end_time' => now()->addMinutes(150)->format('Y-m-d\TH:i'),
        ];

        $response = $this->actingAs($customer)->post(route('reservations.store'), $overlappingData);
        $response->assertSessionHasErrors('start_time');
    }
}
