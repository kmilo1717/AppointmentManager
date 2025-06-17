<?php

namespace App\Providers;

use App\Events\CitaCreated;
use App\Listeners\SendCitaCreatedNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CitaCreated::class => [
            SendCitaCreatedNotification::class,
        ],
    ];

    public function boot()
    {
        //
    }

    public function shouldDiscoverEvents()
    {
        return false;
    }
}
