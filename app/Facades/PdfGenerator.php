<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use Siak\Tontine\Service\Report\PdfGeneratorInterface;

/**
 * @method static string getPdf(string $html)
 */
class PdfGenerator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PdfGeneratorInterface::class;
    }
}
