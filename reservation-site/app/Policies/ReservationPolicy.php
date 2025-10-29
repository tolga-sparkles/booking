<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class ReservationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return Gate::forUser($user)->allows('is_admin_or_manager');
    }

    public function view(User $user, Reservation $reservation)
    {
        return Gate::forUser($user)->allows('is_admin_or_manager')
            || $user->id === $reservation->user_id
            || $user->id === $reservation->expert_id;
    }

    public function create(User $user)
    {
        return Gate::forUser($user)->allows('is_customer');
    }

    public function update(User $user, Reservation $reservation)
    {
        return Gate::forUser($user)->allows('is_admin_or_manager')
            || $user->id === $reservation->user_id
            || $user->id === $reservation->expert_id;
    }

    public function delete(User $user, Reservation $reservation)
    {
        return Gate::forUser($user)->allows('is_admin_or_manager') || $user->id === $reservation->user_id;
    }
}
