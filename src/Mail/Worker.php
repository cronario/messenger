<?php

namespace Messenger\Mail;

use Cronario\AbstractJob;
use Cronario\AbstractWorker;
use Messenger\Template;
use Messenger\TemplateException;

class Worker extends AbstractWorker
{

    protected static $config
        = [
            'client' => [
                'host'   => '...',
                'params' => [
                    '...' => '...',
                ],
            ]
        ];

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

    /**
     * @param $host
     * @param $params
     *
     * @return \Zend_Mail_Transport_Smtp
     */
    public function getTransport($host, $params)
    {
        return new \Zend_Mail_Transport_Smtp($host, $params);
    }

    /**
     * @return \Zend_Mail
     */
    public function getMail()
    {
        return new \Zend_Mail('utf-8');
    }

    /**
     * @param Job $job
     *
     * @return array
     * @throws \Zend_Mail_Exception
     */
    protected function sendMail(Job $job)
    {
        // prepare Transport
        $clientConfig = static::getConfig('client');

        $transport = $this->getTransport(
            $clientConfig['host'],
            $clientConfig['params']
        );

        // Build mail
        $Email = $this->getMail();
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

        // Send mail
        $success = (bool) $Email->send($transport);

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
        $this->validateJobParams($job);

        try {

            // Send message
            $response = $this->sendMail($job);
            $resultData['response'] = $response;

        } catch (\Exception $ex) {
            throw new ResultException(ResultException::RETRY_TRANSPORT_ERROR);
        }

        throw new ResultException(ResultException::R_SUCCESS, $resultData);
    }
}
