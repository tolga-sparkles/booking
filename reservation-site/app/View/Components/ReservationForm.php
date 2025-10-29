<?php

namespace App\View\Components;

use App\Models\Reservation;
use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Collection;

class ReservationForm extends Component
{
    public Collection $experts;
    public ?Reservation $reservation;

    /**
     * Create a new component instance.
     */
    public function __construct(?Reservation $reservation = null)
    {
        $this->experts = User::where('role', 'expert')->get();
        $this->reservation = $reservation;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.reservation-form');
    }
}
