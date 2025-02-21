<?php

namespace Ajax;

use Jaxon\App\Component as BaseComponent;

/**
 * @databag tenant
 * @callback jaxon.ajax.callback.tontine
 */
abstract class Component extends BaseComponent
{
    use ComponentTrait;
}
