<?php

namespace Ajax\Base\Round;

use Ajax\Base\Guild\PageComponent as BaseComponent;
use Jaxon\Attributes\Attribute\Before;

#[Before('getCurrentRound')]
abstract class PageComponent extends BaseComponent
{
    use ComponentTrait;
}
