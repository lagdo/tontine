<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use Siak\Tontine\Service\Report\PdfGeneratorInterface;

class PdfGenerator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PdfGeneratorInterface::class;
    }
}
