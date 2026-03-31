<?php

namespace App\Providers;

use App\Events\OnPagePaymentHome;
use App\Events\OnPagePaymentPayables;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(function(OnPagePaymentHome $event) {
        });
        Event::listen(function(OnPagePaymentPayables $event) {
        });
    }
}
