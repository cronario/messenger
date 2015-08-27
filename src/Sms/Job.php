<?php

namespace Messenger\Sms;

use Cronario\AbstractJob;

class Job extends AbstractJob
{

    const P_PARAM_TEMPLATE = 'template';
    const P_PARAM_RECIPIENT = 'recipient';
    const P_PARAM_SENDER = 'sender';
    const P_PARAM_TEXT = 'text';

    /**
     * @return int|null|string
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
     * @return int|null|string
     */
    public function getRecipient()
    {
        return $this->getParam(self::P_PARAM_RECIPIENT);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setRecipient($value)
    {
        return $this->setParam(self::P_PARAM_RECIPIENT, $value);
    }

    /**
     * @return int|null|string
     */
    public function getSender()
    {
        return $this->getParam(self::P_PARAM_SENDER);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setSender($value)
    {
        return $this->setParam(self::P_PARAM_SENDER, $value);
    }

    /**
     * @return int|null|string
     */
    public function getText()
    {
        return $this->getParam(self::P_PARAM_TEXT);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setText($value)
    {
        return $this->setParam(self::P_PARAM_TEXT, $value);
    }


}