<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use Siak\Tontine\Service\Report\Pdf\GeneratorInterface;

/**
 * @method static string getSessionReport(string $template)
 * @method static string getProfitsReport(string $template)
 * @method static string getRoundReport(string $template)
 */
class PdfGenerator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return GeneratorInterface::class;
    }
}
