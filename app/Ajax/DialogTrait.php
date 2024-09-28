<?php

namespace App\Ajax;

use Jaxon\Plugin\Response\Dialog\MessageInterface;
use Jaxon\Plugin\Response\Dialog\ModalInterface;

trait DialogTrait
{
    /**
     * @var ModalInterface
     */
    public $dialog;

    /**
     * @var MessageInterface
     */
    public $notify;
}
