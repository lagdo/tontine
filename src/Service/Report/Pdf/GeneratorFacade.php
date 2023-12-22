<?php

namespace Siak\Tontine\Service\Report\Pdf;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string getPdf(string $html, array $config)
 */
class GeneratorFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return GeneratorInterface::class;
    }
}
