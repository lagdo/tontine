<?php

namespace Ajax\App\Meeting\Session\Charge;

use Jaxon\Attributes\Attribute\Before;

#[Before('getCharge')]
abstract class FuncComponent extends \Ajax\App\Meeting\Session\FuncComponent
{
    use ComponentTrait;
}
