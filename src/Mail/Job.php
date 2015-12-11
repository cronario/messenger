<?php

namespace Messenger\Mail;

use Cronario\AbstractJob;

class Job extends AbstractJob
{

    const P_PARAM_FROM_MAIL = 'fromMail';
    const P_PARAM_FROM_NAME = 'fromName';
    const P_PARAM_TO_MAIL = 'toMail';
    const P_PARAM_SUBJECT = 'subject';
    const P_PARAM_BODY = 'body';

    const P_PARAM_ATTACHMENT = 'attachment';
    const P_PARAM_ATTACHMENT__PATH = 'p';
    const P_PARAM_ATTACHMENT__NAME = 'n';

    /**
     * @return int|null|string
     */
    public function getFromMail()
    {
        return $this->getParam(self::P_PARAM_FROM_MAIL);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setFromMail($value)
    {
        return $this->setParam(self::P_PARAM_FROM_MAIL, $value);
    }

    /**
     * @return int|null|string
     */
    public function getFromName()
    {
        return $this->getParam(self::P_PARAM_FROM_NAME);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setFromName($value)
    {
        return $this->setParam(self::P_PARAM_FROM_NAME, $value);
    }

    /**
     * @return int|null|string
     */
    public function getToMail()
    {
        return $this->getParam(self::P_PARAM_TO_MAIL);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setToMail($value)
    {
        return $this->setParam(self::P_PARAM_TO_MAIL, $value);
    }

    /**
     * @return int|null|string
     */
    public function getSubject()
    {
        return $this->getParam(self::P_PARAM_SUBJECT);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setSubject($value)
    {
        return $this->setParam(self::P_PARAM_SUBJECT, $value);
    }

    /**
     * @return int|null|string
     */
    public function getBody()
    {
        return $this->getParam(self::P_PARAM_BODY);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setBody($value)
    {
        return $this->setParam(self::P_PARAM_BODY, $value);
    }

    /**
     * @return int|null|string
     */
    public function getAttachment()
    {
        return $this->getParam(self::P_PARAM_ATTACHMENT);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setAttachment($value)
    {
        return $this->setParam(self::P_PARAM_ATTACHMENT, $value);
    }

}