<?php

namespace Messenger\Mail;

use Cronario\AbstractJob;
use Cronario\AbstractWorker;
use Messenger\Template;
use Messenger\TemplateException;

class Worker extends AbstractWorker
{

    // region TEMPLATE ********************************************************

    protected static $config
        = [
            'client' => [
                'host'   => '...',
                'params' => [
                    '...'     => '...',
                ],
            ]
        ];


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

            $job->setFromMail($fields[Job::P_PARAM_FROM_MAIL]);
            $job->setFromName($fields[Job::P_PARAM_FROM_NAME]);
            $job->setToMail($fields[Job::P_PARAM_TO_MAIL]);
            $job->setSubject($fields[Job::P_PARAM_SUBJECT]);
            $job->setBody($fields[Job::P_PARAM_BODY]);

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
        if (empty($job->getFromMail())) {
            throw new ResultException(ResultException::ERROR_PARAM_FROM_MAIL);
        }

        if (empty($job->getFromName())) {
            throw new ResultException(ResultException::ERROR_PARAM_FROM_NAME);
        }

        if (empty($job->getToMail())) {
            throw new ResultException(ResultException::ERROR_PARAM_TO_MAIL);
        }

        if (empty($job->getSubject())) {
            throw new ResultException(ResultException::ERROR_PARAM_SUBJECT);
        }

        if (empty($job->getBody())) {
            throw new ResultException(ResultException::ERROR_PARAM_BODY);
        }

    }

    // endregion *************************************************************


    protected function sendMail(Job $job)
    {
        /**
         * prepare Transport
         */
        $clientConfig = static::getConfig('client');

        $transport = new \Zend_Mail_Transport_Smtp(
            $clientConfig['host'],
            $clientConfig['params']
        );

        /**
         * Build mail
         */
        $Email = new \Zend_Mail('utf-8');
        $Email->setSubject($job->getSubject());
        $Email->setBodyHtml($job->getBody());
        $Email->setBodyText(strip_tags($job->getBody()));
        $Email->setFrom($job->getFromMail(), $job->getFromName());
        $Email->addTo($job->getToMail());

        $attachments = $job->getAttachment();

        if (!empty($attachments) && is_array($attachments) && count($attachments) > 0) {
            foreach ($attachments as $key => $attach) {
                //$at[$key] = $mail->createAttachment(file_get_contents($attachment[Job::T_EMAIL_P_ATTACHMENT__PATH]));
                $at[$key] = $Email->createAttachment(base64_decode($attach[Job::P_PARAM_ATTACHMENT__PATH]));
                $at[$key]->filename = $attach[Job::P_PARAM_ATTACHMENT__NAME];
                $at[$key]->type = $attach[Job::P_PARAM_ATTACHMENT__TYPE];
                $at[$key]->disposition = $attach[Job::P_PARAM_ATTACHMENT__DISPOSITION];
                $at[$key]->encoding = $attach[Job::P_PARAM_ATTACHMENT__ENCODING];
                $at[$key]->id = $attach[Job::P_PARAM_ATTACHMENT__ID];
            }
        }

        /**
         * Send mail
         */
        $success = (bool)$Email->send($transport);

        $response = [
            'success' => $success
        ];

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

            // Send message
            $response = $this->sendMail($job);
            $resultData['response'] = $response;

        } catch (\Exception $ex) {
            throw new ResultException(ResultException::RETRY_TRANSPORT_ERROR);
        }

        if ($response['ok']) {
            // TODO ....
            // ....
        }
        if (!$response['ok']) {
            // TODO ....
            // ....
        }

        throw new ResultException(ResultException::R_SUCCESS, $resultData);
    }
}
