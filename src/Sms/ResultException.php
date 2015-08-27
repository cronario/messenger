<?php

namespace Messenger\Sms;

use Cronario\Exception\ResultException as IkResultException;

class ResultException extends IkResultException
{


    const FAILURE_TRANSPORT_NO_BALANCE = 201;

    const ERROR_BUILD_TEMPLATE = 410;
    const ERROR_PARAM_TEXT = 451;
    const ERROR_PARAM_SENDER = 452;
    const ERROR_PARAM_RECIPIENT = 453;
    const ERROR_TRANSPORT = 454;

    const RETRY_GATEWAY_DISPATCH_CLASS = 601;

    const REDIRECT_GATEWAY_CLASS = 701;

    public static $results
        = array(
            self::R_RETRY                      => array(
                self::P_STATUS  => self::STATUS_RETRY,
                self::P_MESSAGE => 'retry ...',
            ),
            self::FAILURE_TRANSPORT_NO_BALANCE => array(
                self::P_MESSAGE => 'fail, transport no balance ',
                self::P_STATUS  => self::STATUS_FAILURE
            ),
            self::ERROR_BUILD_TEMPLATE         => array(
                self::P_MESSAGE => 'error, build template',
                self::P_STATUS  => self::STATUS_ERROR
            ),
            self::ERROR_PARAM_TEXT             => array(
                self::P_MESSAGE => 'error, param text',
                self::P_STATUS  => self::STATUS_ERROR
            ),
            self::ERROR_PARAM_SENDER           => array(
                self::P_MESSAGE => 'error, param sender',
                self::P_STATUS  => self::STATUS_ERROR
            ),
            self::ERROR_PARAM_RECIPIENT        => array(
                self::P_MESSAGE => 'error, param recipient',
                self::P_STATUS  => self::STATUS_ERROR
            ),
            self::ERROR_TRANSPORT              => array(
                self::P_MESSAGE => 'error, transport',
                self::P_STATUS  => self::STATUS_ERROR
            ),
            self::RETRY_GATEWAY_DISPATCH_CLASS => array(
                self::P_STATUS  => self::STATUS_RETRY,
                self::P_MESSAGE => 'retry gateway dispatch class...',
            ),
            self::REDIRECT_GATEWAY_CLASS       => array(
                self::P_STATUS  => self::STATUS_REDIRECT,
                self::P_MESSAGE => 'redirect ...',
            ),
        );

}
