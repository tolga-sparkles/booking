<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if (Gate::allows('is_admin_or_manager')) {
            return redirect()->route('admin.reservations.index');
        }

        if (Gate::allows('is_expert')) {
            $reservations = $user->reservationsAsExpert()->latest()->get();
        } else {
            $reservations = $user->reservations()->latest()->get();
        }

        return view('reservations.index', compact('reservations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Reservation::class);
        $experts = User::where('role', 'expert')->get();
        // Retrieve pending reservation data from the session, if it exists.
        // Using pull() so it's only available for this one request.
        $pending = $request->session()->pull('pending_reservation', null);

        return view('reservations.create', compact('experts', 'pending'));
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

        $startTime = new \Carbon\Carbon($request->input('start_time'));
        $endTime = new \Carbon\Carbon($request->input('end_time'));
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

        Reservation::create([
            'user_id' => Auth::id(),
            'expert_id' => $expertId,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        return redirect()->route('reservations.index')->with('success', 'Reservation created successfully.');
    }


    /**
     * Prepare a booking from the homepage.
     */
    public function prepareBooking(Request $request)
    {
        // If user is already logged in, just create the reservation directly.
        if (Auth::check()) {
            return $this->store($request);
        }

        // Validate the incoming data from the homepage form.
        $validatedData = $request->validate([
            'expert_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        // Store the validated data in the session to retrieve after login.
        $request->session()->put('pending_reservation', $validatedData);

        // Redirect to the login page with a message.
        return redirect()->route('login')
                         ->with('info', 'Please login or register to complete your booking.');
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

        $startTime = new \Carbon\Carbon($request->input('start_time'));
        $endTime = new \Carbon\Carbon($request->input('end_time'));
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
