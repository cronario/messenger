<?php

namespace Messenger\Hipchat;

use Cronario\AbstractJob;
use Cronario\AbstractWorker;
use Messenger\Template;
use Messenger\TemplateException;

/**
 * Class Worker
 *
 * @package Messenger\Sms\Alpha
 */
class Worker extends AbstractWorker
{

    // region VALIDATE ********************************************************

    /**
     * @param Job $job
     *
     * @throws ResultException
     */
    protected function validateJobParams(Job $job)
    {
        if (empty($job->getToken())) {
            throw new ResultException(ResultException::ERROR_PARAM_TOKEN);
        }

        if (empty($job->getRoom())) {
            throw new ResultException(ResultException::ERROR_PARAM_ROOM);
        }

        if (empty($job->getFrom())) {
            throw new ResultException(ResultException::ERROR_PARAM_FROM);
        }

        if (empty($job->getMsg())) {
            throw new ResultException(ResultException::ERROR_PARAM_MSG);
        }

    }

    // endregion *************************************************************

    /**
     * @param $token
     *
     * @return \HipChat\HipChat
     */
    public function getTransport($token)
    {
        return new \HipChat\HipChat($token);
    }

    /**
     * @param Job $job
     *
     * @return bool
     */
    protected function sendMessage(Job $job)
    {
        // prepare transport
        $transport = $this->getTransport($job->getToken());

        // Send message
        $response = $transport->message_room(
            $job->getRoom(),
            $job->getFrom(),
            $job->getMsg(),
            false,
            $job->getColour() ?: \HipChat\HipChat::COLOR_YELLOW,
            $job->getFormat() ?: \HipChat\HipChat::FORMAT_TEXT
        );

        return $response;
    }

    /**
     * @param AbstractJob|Job $job
     *
     * @throws ResultException
     */
    protected function doJob(AbstractJob $job)
    {
        $this->validateJobParams($job);

        try {
            $resultData['response'] = $this->sendMessage($job);
        } catch (\Exception $ex) {
            throw new ResultException(ResultException::ERROR_TRANSPORT);
        }

        throw new ResultException(ResultException::R_SUCCESS, $resultData);
    }
}