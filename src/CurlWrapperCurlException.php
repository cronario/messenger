<?php

namespace Messenger;

/**
 * CurlWrapper - Flexible wrapper class for PHP cURL extension
 *
 * @author Leonid Svyatov <leonid@svyatov.ru>
 * @copyright 2010-2011, 2014 Leonid Svyatov
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @version 1.3.0
 * @link http://github.com/svyatov/CurlWrapper
 */

/**
 * CurlWrapper cURL Exceptions class
 */
class CurlWrapperCurlException extends CurlWrapperException
{
    /**
     * @param resource $curlHandler
     */
    public function __construct($curlHandler)
    {
        $this->message = curl_error($curlHandler);
        $this->code = curl_errno($curlHandler);
    }
}