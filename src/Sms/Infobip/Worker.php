<?php

namespace Messenger\Sms\Infobip;

use Cronario\AbstractJob;
use Cronario\AbstractWorker;
use Messenger\Sms\Job;
use Messenger\Sms\ResultException;
use Messenger\Sms\Worker as SmsWorker;

/**
 * Class Worker
 *
 * @package Messenger\Sms\Infobip
 */
class Worker extends AbstractWorker
{

    protected static $config
        = [
            AbstractWorker::CONFIG_P_MANAGERS_LIMIT    => 3,
            AbstractWorker::CONFIG_P_MANAGER_POOL_SIZE => 5,
            SmsWorker::GATEWAY_DISPATCH_CLASS          => '\\Messenger\\Sms\\Worker',
            self::CONFIG_P_CLIENT                      => [
                'login'    => 'xxx',
                'password' => 'xxx',
            ],
        ];

    // region CLIENT ********************************************************

    const CONFIG_P_CLIENT = 'client';

    /**
     * @var \infobip\SmsClient
     */
    protected $transport;

    /**
     * @return \infobip\SmsClient
     */
    protected function getTransport()
    {
        if (null === $this->transport) {
            $clientConfig = self::getConfig(self::CONFIG_P_CLIENT);
            $this->transport = new \infobip\SmsClient($clientConfig['login'], $clientConfig['password']);
        }

        return $this->transport;
    }

    // endregion *************************************************************

    // region VALIDATE ********************************************************

    protected function validateJobParams(Job $job)
    {
        if (empty($job->getSender())) {
            throw new ResultException(ResultException::ERROR_PARAM_SENDER);
        }

        if (empty($job->getRecipient())) {
            throw new ResultException(ResultException::ERROR_PARAM_RECIPIENT);
        }

        if (empty($job->getText())) {
            throw new ResultException(ResultException::ERROR_PARAM_TEXT);
        }
    }

    // endregion *************************************************************

    /**
     * @param AbstractJob|Job $job
     *
     * @throws ResultException
     */
    protected function doJob(AbstractJob $job)
    {
        $this->validateJobParams($job);

        $resultData = SmsWorker::buildResultDataDefault(__NAMESPACE__);

        try {
            $transport = $this->getTransport();

            $smsMessage = new \infobip\models\SMSRequest();
            $smsMessage->senderAddress = $job->getSender();
            $smsMessage->address = $job->getRecipient();
            $smsMessage->message = $job->getText();

            $response = $transport->sendSMS($smsMessage);
            $resultData[SmsWorker::P_RESULT_DATA_SUCCESS] = (count($response['errors']) == 0);
            $resultData[SmsWorker::P_RESULT_DATA_VENDOR_ID] = $response['id'];
            $resultData[SmsWorker::P_RESULT_DATA_ERRORS] = $response['errors'];

            $job->addDebug(['vendor_response' => $response]);

        } catch (\Exception $ex) {
            $job->addDebug(['exception' => $ex->getMessage()]);
            print_r($ex->getMessage());
            throw new ResultException(ResultException::ERROR_TRANSPORT);
        }


        if (false === $resultData[SmsWorker::P_RESULT_DATA_SUCCESS]) {

            if (!$job->isSync()) {
                // redirect result for new root gateway class
                $gatewayClass = static::getConfig(SmsWorker::GATEWAY_DISPATCH_CLASS);
                $job->addDebug(['set_gateway' => $gatewayClass]);
                $job->setWorkerClass($gatewayClass)->save();
                throw new ResultException(ResultException::RETRY_GATEWAY_DISPATCH_CLASS);
            }

            throw new ResultException(ResultException::R_FAILURE, $resultData);
        }

        throw new ResultException(ResultException::R_SUCCESS, $resultData);
    }

}






