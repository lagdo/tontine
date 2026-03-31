<?php

namespace Ajax\Base\Round;

use Ajax\Base\Guild\FuncComponent as BaseComponent;
use Jaxon\Attributes\Attribute\Before;

#[Before('getCurrentRound')]
abstract class FuncComponent extends BaseComponent
{
    use ComponentTrait;
}
