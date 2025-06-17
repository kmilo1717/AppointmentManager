<?php

namespace App\Events;

use App\Models\Appointment;
use App\Models\Cita;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CitaCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $cita;

    public function __construct(Cita $cita)
    {
        $this->cita = $cita;
    }
}
