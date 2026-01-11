<?php

namespace Ajax\App\Meeting\Summary;

use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;

#[Before('checkHostAccess', ["meeting", "sessions"])]
#[Before('getSession')]
#[Databag('summary')]
abstract class FuncComponent extends \Ajax\Base\Round\FuncComponent
{
    use ComponentTrait;
}
