<?php

namespace Ajax\App\Meeting\Session;

use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;

#[Before('checkHostAccess', ["meeting", "sessions"])]
#[Before('getSession')]
#[Databag('meeting')]
abstract class Component extends \Ajax\Base\Round\Component
{
    use ComponentTrait;
}
