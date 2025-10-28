<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function view(User $user, Reservation $reservation)
    {
        return $user->isAdmin()
            || $user->isManager()
            || $user->id === $reservation->user_id
            || $user->id === $reservation->expert_id;
    }

    public function create(User $user)
    {
        return $user->isCustomer();
    }

    public function update(User $user, Reservation $reservation)
    {
        return $user->isAdmin()
            || $user->id === $reservation->user_id
            || $user->id === $reservation->expert_id;
    }

    public function delete(User $user, Reservation $reservation)
    {
        return $user->isAdmin() || $user->id === $reservation->user_id;
    }
}
