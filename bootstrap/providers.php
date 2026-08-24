<?php

use App\Providers;

return [
    Providers\AppServiceProvider::class,
    Providers\EventServiceProvider::class,
    Providers\FortifyServiceProvider::class,
    /*
    * Package Service Providers...
    */
    Providers\SiakServiceProvider::class,
    Providers\SiakExtServiceProvider::class,
    Providers\VoletServiceProvider::class,
];
