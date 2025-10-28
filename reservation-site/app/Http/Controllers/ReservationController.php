<?php

namespace App\Http\Controllers;

use App\Mail\ReservationCancelled;
use App\Mail\ReservationCreated;
use App\Mail\ReservationUpdated;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin() || $user->isManager()) {
            return redirect()->route('admin.reservations.index');
        }

        if ($user->isExpert()) {
            $reservations = $user->reservationsAsExpert()->latest()->get();
        } else {
            $reservations = $user->reservations()->latest()->get();
        }

        return view('reservations.index', compact('reservations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Reservation::class);
        $experts = User::where('role', 'expert')->get();
        return view('reservations.create', compact('experts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Reservation::class);
        $request->validate([
            'expert_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        $expertId = $request->input('expert_id');

        // Check for overlapping reservations for the selected expert
        $overlappingReservations = Reservation::where('expert_id', $expertId)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
            })->exists();

        if ($overlappingReservations) {
            return back()->withErrors(['start_time' => 'The selected time slot is already booked for this expert.'])->withInput();
        }

        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'expert_id' => $expertId,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        // Send email notifications
        Mail::to($reservation->user)->send(new ReservationCreated($reservation));
        Mail::to($reservation->expert)->send(new ReservationCreated($reservation));

        return redirect()->route('reservations.index')->with('success', 'Reservation created successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);

        return view('reservations.show', compact('reservation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        $experts = User::where('role', 'expert')->get();
        return view('reservations.edit', compact('reservation', 'experts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        $request->validate([
            'expert_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        $expertId = $request->input('expert_id');

        // Check for overlapping reservations for the selected expert, excluding the current one
        $overlappingReservations = Reservation::where('expert_id', $expertId)
            ->where('id', '!=', $reservation->id)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
            })->exists();

        if ($overlappingReservations) {
            return back()->withErrors(['start_time' => 'The selected time slot is already booked for this expert.'])->withInput();
        }

        $reservation->update([
            'expert_id' => $expertId,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        // Send email notifications
        Mail::to($reservation->user)->send(new ReservationUpdated($reservation));
        Mail::to($reservation->expert)->send(new ReservationUpdated($reservation));

        return redirect()->route('reservations.index')->with('success', 'Reservation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);

        // Send email notifications before deleting
        Mail::to($reservation->user)->send(new ReservationCancelled($reservation));
        Mail::to($reservation->expert)->send(new ReservationCancelled($reservation));

        $reservation->delete();

        return redirect()->route('reservations.index')->with('success', 'Reservation deleted successfully.');
    }
}
