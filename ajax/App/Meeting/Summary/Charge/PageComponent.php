<?php

namespace Ajax\App\Meeting\Summary\Charge;

use Jaxon\Attributes\Attribute\Before;

#[Before('getCharge')]
abstract class PageComponent extends \Ajax\App\Meeting\Summary\PageComponent
{
    use ComponentTrait;
}
