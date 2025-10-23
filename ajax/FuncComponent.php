<?php

namespace Ajax;

use Jaxon\App\FuncComponent as BaseComponent;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Databag;

#[Databag('tenant')]
#[Callback('jaxon.ajax.callback.tontine')]
class FuncComponent extends BaseComponent
{
    use ComponentTrait;
}
