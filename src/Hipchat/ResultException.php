<?php

namespace Messenger\Hipchat;

use Cronario\Exception\ResultException as IkResultException;

class ResultException extends IkResultException
{

    const ERROR_TRANSPORT = 401;

    const ERROR_BUILD_TEMPLATE = 410;

    const ERROR_PARAM_TOKEN = 451;
    const ERROR_PARAM_ROOM = 452;
    const ERROR_PARAM_FROM = 453;
    const ERROR_PARAM_MSG = 454;

    /**
     * @var array
     */
    public static $results
        = array(
            self::ERROR_TRANSPORT      => array(
                self::P_MESSAGE => 'error, transport',
                self::P_STATUS  => self::STATUS_ERROR
            ),
            self::ERROR_BUILD_TEMPLATE => array(
                self::P_MESSAGE => 'error, build template',
                self::P_STATUS  => self::STATUS_ERROR
            ),
            self::ERROR_PARAM_TOKEN    => array(
                self::P_MESSAGE => 'error, param token',
                self::P_STATUS  => self::STATUS_ERROR
            ),
            self::ERROR_PARAM_ROOM     => array(
                self::P_MESSAGE => 'error, param room',
                self::P_STATUS  => self::STATUS_ERROR
            ),
            self::ERROR_PARAM_FROM     => array(
                self::P_MESSAGE => 'error, param from',
                self::P_STATUS  => self::STATUS_ERROR
            ),
            self::ERROR_PARAM_MSG      => array(
                self::P_MESSAGE => 'error, param msg',
                self::P_STATUS  => self::STATUS_ERROR
            ),
        );


}