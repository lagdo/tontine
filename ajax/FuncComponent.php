<?php

namespace Ajax;

use Jaxon\App\FuncComponent as BaseComponent;
use Jaxon\Attributes\Attribute\Databag;

#[Databag('tenant')]
class FuncComponent extends BaseComponent
{
    use ComponentTrait;
}
