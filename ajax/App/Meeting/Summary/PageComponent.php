<?php

namespace Ajax\App\Meeting\Summary;

use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;

#[Before('checkHostAccess', ["meeting", "sessions"])]
#[Before('getSession')]
#[Databag('summary')]
abstract class PageComponent extends \Ajax\Base\Round\PageComponent
{
    use ComponentTrait;
}
