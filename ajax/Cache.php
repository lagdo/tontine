<?php

namespace Ajax;

use Illuminate\Support\Facades\Facade;

class Cache extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Siak\Tontine\Cache\Cache::class;
    }
}
