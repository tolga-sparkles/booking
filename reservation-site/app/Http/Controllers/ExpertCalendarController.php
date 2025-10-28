<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpertCalendarController extends Controller
{
    /**
     * Display the expert's calendar.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('expert.calendar');
    }

    /**
     * Fetch reservation data for the calendar.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function events()
    {
        $reservations = Auth::user()->reservationsAsExpert()
            ->with('user')
            ->get(['id', 'user_id', 'start_time', 'end_time']);

        $events = $reservations->map(function ($reservation) {
            return [
                'title' => 'Reservation with ' . $reservation->user->name,
                'start' => $reservation->start_time,
                'end' => $reservation->end_time,
                'url' => route('reservations.show', $reservation->id),
            ];
        });

        return response()->json($events);
    }
}
