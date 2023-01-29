<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use Siak\Tontine\Service\Report\ReportServiceInterface;

class Report extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ReportServiceInterface::class;
    }
}
