<?php

namespace Messenger\Sms\Alpha;

use Cronario\AbstractJob;
use Cronario\AbstractWorker;
use Messenger\Sms\Job;
use Messenger\Sms\ResultException;
use Messenger\Template;
use Messenger\TemplateException;
use Messenger\Sms\Worker as SmsWorker;

/**
 * Class Worker
 *
 * @package Messenger\Sms\Alpha
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
                'server'   => 'xxx',
                'version'  => 'xxx',
            ],
        ];

    // region CLIENT ********************************************************

    const CONFIG_P_CLIENT = 'client';

    /**
     * @var \AlphaSMS\Client
     */
    protected $transport;

    /**
     * @return \AlphaSMS\Client
     */
    protected function getTransport()
    {
        if (null === $this->transport) {
            $clientConfig = self::getConfig(self::CONFIG_P_CLIENT);
            $this->transport = new \AlphaSMS\Client($clientConfig['login'], $clientConfig['password']);
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
            $transport->sendSMS($job->getSender(), $job->getRecipient(), $job->getText());

            $response = $transport->getResponse();
            $resultData[SmsWorker::P_RESULT_DATA_SUCCESS] = (count($response['errors']) == 0);
            $resultData[SmsWorker::P_RESULT_DATA_VENDOR_ID] = $response['id'];
            $resultData[SmsWorker::P_RESULT_DATA_ERRORS] = $response['errors'];

            $job->addDebugData('vendor_response', $response);

        } catch (\Exception $ex) {
            $job->addDebugData('exception', $ex->getMessage());
            throw new ResultException(ResultException::ERROR_TRANSPORT);
        }


        if (false === $resultData[SmsWorker::P_RESULT_DATA_SUCCESS]) {

            if (!$job->isSync()) {
                // redirect result for new root gateway class
                $gatewayClass = static::getConfig(SmsWorker::GATEWAY_DISPATCH_CLASS);
                $job->addDebugData('set_gateway', $gatewayClass);
                $job->setWorkerClass($gatewayClass)->save();
                throw new ResultException(ResultException::RETRY_GATEWAY_DISPATCH_CLASS);
            }

            throw new ResultException(ResultException::R_FAILURE, $resultData);
        }

        throw new ResultException(ResultException::R_SUCCESS, $resultData);
    }

}






