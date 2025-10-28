<?php

use App\Http\Controllers\AdminReservationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ExpertCalendarController;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/', function () {
    $experts = User::where('role', 'expert')->get();
    return view('welcome', compact('experts'));
});

Route::post('/prepare-booking', [ReservationController::class, 'prepareBooking'])->name('prepare-booking');

use App\Models\Reservation;

Route::get('/dashboard', function () {
    $totalUsers = User::count();
    $totalExperts = User::where('role', 'expert')->count();
    $totalReservations = Reservation::count();

    return view('dashboard', compact('totalUsers', 'totalExperts', 'totalReservations'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('reservations', ReservationController::class);
});

// Admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('admin/reservations', AdminReservationController::class, ['as' => 'admin']);
});

// Expert routes
Route::middleware(['auth', 'expert'])->prefix('expert')->name('expert.')->group(function () {
    Route::get('calendar', [ExpertCalendarController::class, 'index'])->name('calendar');
});

// API routes for authenticated experts
Route::middleware(['auth', 'expert'])->prefix('api/expert')->name('api.expert.')->group(function () {
    Route::get('reservations', [ExpertCalendarController::class, 'events'])->name('reservations');
});

require __DIR__.'/auth.php';
