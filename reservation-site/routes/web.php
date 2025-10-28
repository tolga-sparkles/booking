<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AdminReservationController;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/', function () {
    $experts = User::where('role', 'expert')->get();
    return view('welcome', compact('experts'));
});

Route::post('/prepare-booking', [ReservationController::class, 'prepareBooking'])->name('prepare-booking');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('reservations', ReservationController::class);
});

// Admin routes
Route::middleware(['auth', 'admin'])->name('admin.')->prefix('admin')->group(function () {
    Route::resource('reservations', AdminReservationController::class);
    Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update']);
});


require __DIR__.'/auth.php';
