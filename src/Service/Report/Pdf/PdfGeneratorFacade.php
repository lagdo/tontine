<?php

namespace Siak\Tontine\Service\Report\Pdf;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string getPdf(string $html, array $config)
 */
class PdfGeneratorFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PdfGeneratorInterface::class;
    }
}
