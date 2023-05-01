<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use Siak\Tontine\Service\Report\ReportServiceInterface;

/**
 * @method static array getPoolReport(int $poolId)
 * @method static array getSessionReport(int $sessionId)
 */
class Report extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ReportServiceInterface::class;
    }
}
