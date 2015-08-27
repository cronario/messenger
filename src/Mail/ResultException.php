<?php

namespace Messenger\Mail;

use Cronario\Exception\ResultException as IkResultException;

class ResultException extends IkResultException
{
    const ERROR_TRANSPORT = 401;

    const ERROR_BUILD_TEMPLATE = 410;

    const ERROR_PARAM_FROM_MAIL = 451;
    const ERROR_PARAM_FROM_NAME = 452;
    const ERROR_PARAM_TO_MAIL = 453;
    const ERROR_PARAM_SUBJECT = 454;
    const ERROR_PARAM_BODY = 455;

    const RETRY_TRANSPORT_ERROR = 601;

    public static $results
        = array(

            self::ERROR_TRANSPORT       => array(
                self::P_MESSAGE => 'error, transport',
                self::P_STATUS  => self::STATUS_ERROR
            ),
            self::ERROR_BUILD_TEMPLATE  => array(
                self::P_MESSAGE => 'error, build template',
                self::P_STATUS  => self::STATUS_ERROR
            ),
            self::ERROR_PARAM_FROM_MAIL => array(
                self::P_MESSAGE => 'error, param from mail',
                self::P_STATUS  => self::STATUS_ERROR
            ),
            self::ERROR_PARAM_FROM_NAME => array(
                self::P_MESSAGE => 'error, param from name',
                self::P_STATUS  => self::STATUS_ERROR
            ),
            self::ERROR_PARAM_TO_MAIL   => array(
                self::P_MESSAGE => 'error, param to mail',
                self::P_STATUS  => self::STATUS_ERROR
            ),
            self::ERROR_PARAM_SUBJECT   => array(
                self::P_MESSAGE => 'error, param subject',
                self::P_STATUS  => self::STATUS_ERROR
            ),
            self::ERROR_PARAM_BODY      => array(
                self::P_MESSAGE => 'error, param body',
                self::P_STATUS  => self::STATUS_ERROR
            ),
            self::RETRY_TRANSPORT_ERROR       => array(
                self::P_MESSAGE => 'error, transport , retry later',
                self::P_STATUS  => self::STATUS_RETRY
            ),
        );

}
