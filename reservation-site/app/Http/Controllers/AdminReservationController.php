<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminReservationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of all reservations.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->authorize('viewAny', Reservation::class);
        $reservations = Reservation::with('user', 'expert')->latest()->get();

        return view('admin.reservations.index', compact('reservations'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\View\View
     */
    public function edit(Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        $experts = User::where('role', 'expert')->get();
        return view('admin.reservations.edit', compact('reservation', 'experts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        $request->validate([
            'expert_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $reservation->update($request->all());

        return redirect()->route('admin.reservations.index')->with('success', 'Reservation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);
        $reservation->delete();

        return redirect()->route('admin.reservations.index')->with('success', 'Reservation deleted successfully.');
    }
}
