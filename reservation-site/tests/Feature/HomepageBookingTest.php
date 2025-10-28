<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_booking_from_homepage()
    {
        $expert = User::factory()->create(['role' => 'expert']);

        $reservationData = [
            'expert_id' => $expert->id,
            'start_time' => now()->addHours(1)->format('Y-m-d\TH:i'),
            'end_time' => now()->addHours(2)->format('Y-m-d\TH:i'),
        ];

        $response = $this->post(route('book-appointment'), $reservationData);

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_customer_can_book_from_homepage()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $expert = User::factory()->create(['role' => 'expert']);

        $reservationData = [
            'expert_id' => $expert->id,
            'start_time' => now()->addHours(1)->format('Y-m-d\TH:i'),
            'end_time' => now()->addHours(2)->format('Y-m-d\TH:i'),
        ];

        $response = $this->actingAs($customer)->post(route('book-appointment'), $reservationData);

        $response->assertRedirect(route('reservations.index'));
        $this->assertDatabaseHas('reservations', [
            'user_id' => $customer->id,
            'expert_id' => $expert->id,
        ]);
    }
}
