<?php

namespace Messenger\Curl;

use Cronario\AbstractJob;

class Job extends AbstractJob
{

    const P_PARAM_URL = 'url';
    const P_PARAM_METHOD = 'method';
    const P_PARAM_REQUEST_PARAMS = 'requestParams';
    const P_PARAM_EXPECT_CONTENT = 'expectContent';
    const P_PARAM_EXPECT_CODE = 'expectCode';
    const P_PARAM_SAVE_CONTENT = 'saveContent';
    const P_PARAM_SAVE_INFO = 'saveInfo';


    /**
     * @return null
     */
    public function getUrl()
    {
        return $this->getParam(self::P_PARAM_URL);
    }

    /**
     * @param $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        return $this->setParam(self::P_PARAM_URL, $url);
    }

    /**
     * @return null
     */
    public function getMethod()
    {
        return $this->getParam(self::P_PARAM_METHOD);
    }

    /**
     * @param $method
     *
     * @return $this
     */
    public function setMethod($method)
    {
        return $this->setParam(self::P_PARAM_METHOD, $method);
    }

    /**
     * @return null|array
     */
    public function getRequestParams()
    {
        return $this->getParam(self::P_PARAM_REQUEST_PARAMS);
    }

    /**
     * @param $requestParams
     *
     * @return $this
     */
    public function setRequestParams($requestParams)
    {
        return $this->setParam(self::P_PARAM_REQUEST_PARAMS, $requestParams);
    }

    /**
     * @return null
     */
    public function getExpectContent()
    {
        return $this->getParam(self::P_PARAM_EXPECT_CONTENT);
    }

    /**
     * @param $text
     *
     * @return $this
     */
    public function setExpectContent($text)
    {
        return $this->setParam(self::P_PARAM_EXPECT_CONTENT, $text);
    }

    /**
     * @return null
     */
    public function getExpectCode()
    {
        return $this->getParam(self::P_PARAM_EXPECT_CODE);
    }

    /**
     * @param $text
     *
     * @return $this
     */
    public function setExpectCode($text)
    {
        return $this->setParam(self::P_PARAM_EXPECT_CODE, $text);
    }

    /**
     * @return null
     */
    public function getSaveContent()
    {
        return $this->getParam(self::P_PARAM_SAVE_CONTENT);
    }

    /**
     * @param $boo
     *
     * @return $this
     */
    public function setSaveContent($boo)
    {
        return $this->setParam(self::P_PARAM_SAVE_CONTENT, $boo);
    }

    /**
     * @return null
     */
    public function getSaveInfo()
    {
        return $this->getParam(self::P_PARAM_SAVE_INFO);
    }

    /**
     * @param $boo
     *
     * @return $this
     */
    public function setSaveInfo($boo)
    {
        return $this->setParam(self::P_PARAM_SAVE_INFO, $boo);
    }

}