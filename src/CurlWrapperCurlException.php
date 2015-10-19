<?php

namespace Messenger;


class CurlWrapperCurlException extends CurlWrapperException
{
    /**
     * @param resource $curlHandler
     */
    public function __construct($curlHandler)
    {
        $this->code = curl_errno($curlHandler);
        $this->message = curl_error($curlHandler);
    }
}