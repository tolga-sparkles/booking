<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class AdminReservationController extends Controller
{
    /**
     * Display a listing of all reservations.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $reservations = Reservation::with('user')->latest()->get();

        return view('admin.reservations.index', compact('reservations'));
    }
}
