<?php

namespace Ajax\App\Meeting\Session\Charge;

use Jaxon\Attributes\Attribute\Before;

#[Before('getCharge')]
abstract class PageComponent extends \Ajax\App\Meeting\Session\PageComponent
{
    use ComponentTrait;
}
