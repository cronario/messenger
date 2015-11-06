<?php

namespace Messenger\Curl;

use Cronario\Exception\ResultException as IkResultException;

class ResultException extends IkResultException
{

    const FAILURE_EXPECTED_CONTENT = 201;
    const FAILURE_EXPECTED_HTTP_CODE = 202;

    const ERROR_CURL = 401;

    const RETRY_EXPECTED_CONTENT = 601;
    const RETRY_EXPECTED_HTTP_CODE = 602;

    /**
     * @var array
     */
    public static $results
        = array(

            self::FAILURE_EXPECTED_CONTENT   => array(
                self::P_MESSAGE => 'fail, expected content',
                self::P_STATUS  => self::STATUS_FAILURE
            ),
            self::FAILURE_EXPECTED_HTTP_CODE => array(
                self::P_MESSAGE => 'fail, expected http code',
                self::P_STATUS  => self::STATUS_FAILURE
            ),
            self::ERROR_CURL              => array(
                self::P_MESSAGE => 'error, curl',
                self::P_STATUS  => self::STATUS_ERROR
            ),
            self::RETRY_EXPECTED_CONTENT => array(
                self::P_STATUS  => self::STATUS_RETRY,
                self::P_MESSAGE => 'retry gateway dispatch class...',
            ),
            self::RETRY_EXPECTED_HTTP_CODE => array(
                self::P_STATUS  => self::STATUS_RETRY,
                self::P_MESSAGE => 'retry gateway dispatch class...',
            ),
        );

}