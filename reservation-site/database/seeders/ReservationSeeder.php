<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $experts = User::where('role', 'expert')->get();
        $customers = User::where('role', 'customer')->get();

        if ($experts->isEmpty() || $customers->isEmpty()) {
            $this->command->info('No experts or customers found, skipping reservation seeding.');
            return;
        }

        for ($i = 0; $i < 15; $i++) {
            $expert = $experts->random();
            $customer = $customers->random();
            $startTime = Carbon::now()->addDays(rand(1, 30))->addHours(rand(9, 17));

            Reservation::factory()->create([
                'user_id' => $customer->id,
                'expert_id' => $expert->id,
                'start_time' => $startTime,
                'end_time' => $startTime->copy()->addHour(),
            ]);
        }
    }
}
