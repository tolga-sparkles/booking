<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservations = Reservation::where('user_id', Auth::id())->latest()->get();
        return view('reservations.index', compact('reservations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('reservations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');

        // Check for overlapping reservations
        $overlappingReservations = Reservation::where(function ($query) use ($startTime, $endTime) {
            $query->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
        })->exists();

        if ($overlappingReservations) {
            return back()->withErrors(['start_time' => 'The selected time slot is already booked.'])->withInput();
        }

        Reservation::create([
            'user_id' => Auth::id(),
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        return redirect()->route('reservations.index')->with('success', 'Reservation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        return view('reservations.edit', compact('reservation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');

        // Check for overlapping reservations, excluding the current one
        $overlappingReservations = Reservation::where('id', '!=', $reservation->id)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
            })->exists();

        if ($overlappingReservations) {
            return back()->withErrors(['start_time' => 'The selected time slot is already booked.'])->withInput();
        }

        $reservation->update([
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        return redirect()->route('reservations.index')->with('success', 'Reservation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);

        $reservation->delete();

        return redirect()->route('reservations.index')->with('success', 'Reservation deleted successfully.');
    }
}
