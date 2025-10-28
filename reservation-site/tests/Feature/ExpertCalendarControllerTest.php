<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpertCalendarControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_expert_can_view_their_calendar()
    {
        $expert = User::factory()->create(['role' => 'expert']);

        $response = $this->actingAs($expert)->get(route('expert.calendar'));

        $response->assertStatus(200);
        $response->assertViewIs('expert.calendar');
    }

    public function test_calendar_events_api_returns_correct_data()
    {
        $expert = User::factory()->create(['role' => 'expert']);
        $otherExpert = User::factory()->create(['role' => 'expert']);
        $customer = User::factory()->create(['role' => 'customer']);

        $reservation1 = Reservation::factory()->create(['expert_id' => $expert->id, 'user_id' => $customer->id]);
        $reservation2 = Reservation::factory()->create(['expert_id' => $expert->id, 'user_id' => $customer->id]);
        // This reservation should not be in the results
        Reservation::factory()->create(['expert_id' => $otherExpert->id, 'user_id' => $customer->id]);

        $response = $this->actingAs($expert)->getJson(route('api.expert.reservations'));

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonFragment([
            'title' => 'Reservation with ' . $customer->name,
            'start' => $reservation1->start_time->toISOString(),
        ]);
        $response->assertJsonFragment([
            'title' => 'Reservation with ' . $customer->name,
            'start' => $reservation2->start_time->toISOString(),
        ]);
    }

    public function test_non_expert_cannot_view_calendar()
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get(route('expert.calendar'));

        $response->assertStatus(403);
    }
}
