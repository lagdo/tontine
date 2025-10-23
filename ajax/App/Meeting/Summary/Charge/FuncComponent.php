<?php

namespace Ajax\App\Meeting\Summary\Charge;

use Jaxon\Attributes\Attribute\Before;

#[Before('getCharge')]
abstract class FuncComponent extends \Ajax\App\Meeting\Summary\FuncComponent
{
    use ComponentTrait;
}
