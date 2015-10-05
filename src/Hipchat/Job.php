<?php

namespace Messenger\Hipchat;

use Cronario\AbstractJob;

class Job extends AbstractJob
{

    const P_PARAM_TEMPLATE = 'template';
    const P_PARAM_TOKEN = 'token';
    const P_PARAM_ROOM = 'room';
    const P_PARAM_FROM = 'from';
    const P_PARAM_MSG = 'msg';
    const P_PARAM_COLOUR = 'colour'; // yellow ...
    const P_PARAM_COLOUR_T_RANDOM = \HipChat\HipChat::COLOR_RANDOM;
    const P_PARAM_COLOUR_T_YELLOW = \HipChat\HipChat::COLOR_YELLOW;
    const P_PARAM_COLOUR_T_GRAY = \HipChat\HipChat::COLOR_GRAY;
    const P_PARAM_COLOUR_T_GREEN = \HipChat\HipChat::COLOR_GREEN;
    const P_PARAM_COLOUR_T_PURPLE = \HipChat\HipChat::COLOR_PURPLE;
    const P_PARAM_COLOUR_T_RED = \HipChat\HipChat::COLOR_RED;
    const P_PARAM_FORMAT = 'format'; // text
    const P_PARAM_FORMAT_T_TEXT = \HipChat\HipChat::FORMAT_TEXT;
    const P_PARAM_FORMAT_T_HTML = \HipChat\HipChat::FORMAT_HTML;


    /**
     * @return null
     */
    public function getTemplate()
    {
        return $this->getParam(self::P_PARAM_TEMPLATE);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setTemplate($value)
    {
        return $this->setParam(self::P_PARAM_TEMPLATE, $value);
    }

    /**
     * @return null
     */
    public function getToken()
    {
        return $this->getParam(self::P_PARAM_TOKEN);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setToken($value)
    {
        return $this->setParam(self::P_PARAM_TOKEN, $value);
    }

    /**
     * @return null
     */
    public function getRoom()
    {
        return $this->getParam(self::P_PARAM_ROOM);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setRoom($value)
    {
        return $this->setParam(self::P_PARAM_ROOM, $value);
    }

    /**
     * @return null
     */
    public function getFrom()
    {
        return $this->getParam(self::P_PARAM_FROM);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setFrom($value)
    {
        return $this->setParam(self::P_PARAM_FROM, $value);
    }

    /**
     * @return null
     */
    public function getMsg()
    {
        return $this->getParam(self::P_PARAM_MSG);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setMsg($value)
    {
        return $this->setParam(self::P_PARAM_MSG, $value);
    }

    /**
     * @return null
     */
    public function getColour()
    {
        return $this->getParam(self::P_PARAM_COLOUR);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setColour($value)
    {
        return $this->setParam(self::P_PARAM_COLOUR, $value);
    }

    /**
     * @return null
     */
    public function getFormat()
    {
        return $this->getParam(self::P_PARAM_FORMAT);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setFormat($value)
    {
        return $this->setParam(self::P_PARAM_FORMAT, $value);
    }


}