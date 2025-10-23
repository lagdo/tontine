<?php

namespace Ajax\App\Meeting\Summary\Charge;

use Jaxon\Attributes\Attribute\Before;

#[Before('getCharge')]
abstract class Component extends \Ajax\App\Meeting\Summary\Component
{
    use ComponentTrait;
}
