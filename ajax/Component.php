<?php

namespace Ajax;

use Jaxon\App\NodeComponent as BaseComponent;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;

#[Databag('tenant')]
#[Callback('jaxon.ajax.callback.tontine')]
abstract class Component extends BaseComponent
{
    use ComponentTrait;
}
