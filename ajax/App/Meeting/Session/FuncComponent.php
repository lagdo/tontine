<?php

namespace Ajax\App\Meeting\Session;

use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;

#[Before('checkHostAccess', ["meeting", "sessions"])]
#[Before('getSession')]
#[Databag('meeting')]
abstract class FuncComponent extends \Ajax\Base\Round\FuncComponent
{
    use ComponentTrait;
}
