<?php

namespace Ajax\Base\Guild;

use Ajax\Base\PageComponent as BaseComponent;
use Jaxon\Attributes\Attribute\Before;

#[Before('getCurrentGuild')]
abstract class PageComponent extends BaseComponent
{
    use ComponentTrait;
}
