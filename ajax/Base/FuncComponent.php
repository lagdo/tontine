<?php

namespace Ajax\Base;

use Jaxon\App\FuncComponent as BaseComponent;
use Jaxon\Attributes\Attribute\Databag;

#[Databag('tenant')]
abstract class FuncComponent extends BaseComponent
{
    use ComponentTrait;
}
