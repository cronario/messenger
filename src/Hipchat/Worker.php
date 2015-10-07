<?php

namespace Messenger\Hipchat;

use Cronario\AbstractJob;
use Cronario\AbstractWorker;
use HipChat\HipChat;
use Messenger\Template;
use Messenger\TemplateException;

/**
 * Class Worker
 *
 * @package Messenger\Sms\Alpha
 */
class Worker extends AbstractWorker
{

    // region TEMPLATE ********************************************************

    /**
     * @param Job $job
     *
     * @throws ResultException|null
     */
    protected function buildTemplate(Job $job)
    {
        try {
            $args = $job->getTemplate();
            $template = new Template($args[0], $args[1], $args[2]);
            $fields = $template->make();

            $job->setToken($fields[Job::P_PARAM_TOKEN]);
            $job->setRoom($fields[Job::P_PARAM_ROOM]);
            $job->setFrom($fields[Job::P_PARAM_FROM]);
            $job->setMsg($fields[Job::P_PARAM_MSG]);

            $job->setTemplate(null);
            $job->save();

        } catch (TemplateException $ex) {
            throw new ResultException(ResultException::ERROR_BUILD_TEMPLATE);
        }
    }

    // endregion *************************************************************


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

    public function getTransport($token)
    {
        return new HipChat($token);
    }

    /**
     * @param AbstractJob|Job $job
     *
     * @return bool
     */
    protected function sendMessage(AbstractJob $job)
    {
        // prepare transport
        $transport = $this->getTransport($job->getToken());

        // Send message
        $response = $transport->message_room(
            $job->getRoom(),
            $job->getFrom(),
            $job->getMsg(),
            false,
            $job->getColour() ?: HipChat::COLOR_YELLOW,
            $job->getFormat() ?: HipChat::FORMAT_TEXT
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
        if (is_array($job->getTemplate())) {
            $this->buildTemplate($job);
        }

        $this->validateJobParams($job);

        try {
            $resultData['response'] = $this->sendMessage($job);
        } catch (\Exception $ex) {
            throw new ResultException(ResultException::ERROR_TRANSPORT);
        }

        throw new ResultException(ResultException::R_SUCCESS, $resultData);
    }
}