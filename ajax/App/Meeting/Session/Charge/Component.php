<?php

namespace Ajax\App\Meeting\Session\Charge;

use Jaxon\Attributes\Attribute\Before;

#[Before('getCharge')]
abstract class Component extends \Ajax\App\Meeting\Session\Component
{
    use ComponentTrait;
}
