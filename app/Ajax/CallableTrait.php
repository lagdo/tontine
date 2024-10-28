<?php

namespace App\Ajax;

use Jaxon\Plugin\Response\Dialog\MessageInterface;
use Jaxon\Plugin\Response\Dialog\ModalInterface;
use Siak\Tontine\Cache\Cache;

trait CallableTrait
{
    /**
     * @var ModalInterface
     */
    public $dialog;

    /**
     * @var MessageInterface
     */
    public $notify;

    /**
     * @var Cache
     */
    public $cache;
}
