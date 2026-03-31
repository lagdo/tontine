<?php

namespace Ajax\Base\Guild;

use Ajax\Base\Component as BaseComponent;
use Jaxon\Attributes\Attribute\Before;

#[Before('getCurrentGuild')]
abstract class Component extends BaseComponent
{
    use ComponentTrait;
}
