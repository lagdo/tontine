<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('annotations:mkdir', function () {
    $path = storage_path('annotations');
    file_exists($path) || mkdir($path, 0755, false);
})->purpose('Create the cache dir for the Jaxon classes annotations');
