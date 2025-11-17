<?php

namespace Ajax;

use Jaxon\App\NodeComponent as BaseComponent;
use Jaxon\Attributes\Attribute\Databag;

#[Databag('tenant')]
abstract class Component extends BaseComponent
{
    use ComponentTrait;
}
