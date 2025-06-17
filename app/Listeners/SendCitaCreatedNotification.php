<?php

namespace App\Listeners;

use App\Events\CitaCreated;
use App\Mail\CitaCreatedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCitaCreatedNotification implements ShouldQueue
{
    public function handle(CitaCreated $event)
    {
        Mail::to($event->cita->usuario->email)
            ->send(new CitaCreatedMail($event->cita));
    }
}
