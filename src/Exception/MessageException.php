<?php

namespace Siak\Tontine\Exception;

use Exception;

class MessageException extends Exception
{
    /**
     * The constructor
     *
     * @param string $message
     * @param bool $isError
     */
    public function __construct(string $message, public bool $isError = true)
    {
        parent::__construct($message);
    }
}
