<?php

namespace Messenger;


class CurlWrapperException extends \Exception
{
    /**
     * @param string $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }
}