<?php

namespace Ajax\Base\Round;

use Ajax\Base\Guild\Component as BaseComponent;
use Jaxon\Attributes\Attribute\Before;

#[Before('getCurrentRound')]
abstract class Component extends BaseComponent
{
    use ComponentTrait;
}
