<?php

namespace Messenger\Mail;

use Cronario\AbstractJob;
use Cronario\AbstractWorker;

class Worker extends AbstractWorker
{

    protected static $config
        = [
            'client' => [
//                "Mailer" => 'smtp',                               // Set mailer to use SMTP
//                "Host" => 'smtp1.example.com;smtp2.example.com',  // Specify main and backup SMTP servers
//                "SMTPAuth" => true,                               // Enable SMTP authentication
//                "Username" => 'user@example.com',                 // SMTP username
//                "Password" => 'secret',                           // SMTP password
//                "SMTPSecure" => 'tls',                            // Enable TLS encryption, `ssl` also accepted
//                "Port" => 587,                                    // TCP port to connect to
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
     * @return \PHPMailer
     */
    public function getMail()
    {
        $mail = new \PHPMailer();

        $clientConfig = static::getConfig('client');
        if(is_array($clientConfig) && count($clientConfig) > 0){
            foreach($clientConfig as $key => $value){
                $mail->{$key} = $value;
            }
        }

        return $mail;
    }

    /**
     * @param Job $job
     *
     * @return array
     */
    protected function sendMail(Job $job)
    {

        $mail = $this->getMail();

        $mail->isHTML(true);
        $mail->setFrom($job->getFromMail(), $job->getFromName());
        $mail->addAddress($job->getToMail());

        $mail->Subject = $job->getSubject();
        $mail->Body    = $job->getBody();
        $mail->AltBody = strip_tags($job->getBody());

        $attachments = $job->getAttachment();
        if (!empty($attachments) && is_array($attachments) && count($attachments) > 0) {
            foreach ($attachments as $key => $attach) {
                if(isset($attach[Job::P_PARAM_ATTACHMENT__NAME])){
                    $mail->addAttachment($attach[Job::P_PARAM_ATTACHMENT__PATH] , $attach[Job::P_PARAM_ATTACHMENT__NAME]);
                } else {
                    $mail->addAttachment($attach[Job::P_PARAM_ATTACHMENT__PATH]);
                }
            }
        }

        $isSend = $mail->send();

        return [
            'success' => $isSend
        ];
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

            $response = $this->sendMail($job);
            $resultData['response'] = $response;

        } catch (\Exception $ex) {
            throw new ResultException(ResultException::RETRY_TRANSPORT_ERROR);
        }

        if(!$resultData['response']['success']){
            throw new ResultException(ResultException::R_FAILURE, $resultData);
        }

        throw new ResultException(ResultException::R_SUCCESS, $resultData);
    }
}
