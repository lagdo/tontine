<?php

namespace Ajax\Base\Guild;

use Ajax\Base\FuncComponent as BaseComponent;
use Jaxon\Attributes\Attribute\Before;

#[Before('getCurrentGuild')]
abstract class FuncComponent extends BaseComponent
{
    use ComponentTrait;
}
