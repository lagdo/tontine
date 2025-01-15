<?php

namespace Ajax;

use Jaxon\App\Component as JaxonComponent;

/**
 * @databag tenant
 * @callback jaxon.ajax.callback.tontine
 */
abstract class Component extends JaxonComponent
{
    use CallableTrait;
}
