<?php

namespace Messenger\Sms;

use Cronario\AbstractJob;
use Cronario\AbstractWorker;
use Cronario\Exception\WorkerException;

class Worker extends AbstractWorker
{

    //region Config ***********************************************************

    protected static $config
        = [
            AbstractWorker::CONFIG_P_MANAGERS_LIMIT         => 3,
            AbstractWorker::CONFIG_P_MANAGER_POOL_SIZE      => 5,
            AbstractWorker::CONFIG_P_MANAGER_IDLE_DIE_DELAY => 5,
            self::GATEWAY_CLASS_SET                         => [
                '\Messenger\Sms\Alpha\Worker' => [
                    self::GATEWAY_CLASS_P_REGEX_RECIPIENT => '/^(38)/'
                ],
                '\Messenger\Sms\Test\Worker' => [
                    self::GATEWAY_CLASS_P_REGEX_RECIPIENT => '/^(38)/'
                ],
            ],
        ];

    //endregion ****************************************************************

    //region GATEWAY ***********************************************************

    const GATEWAY_DISPATCH_CLASS = 'gatewayDispatchClass';
    const GATEWAY_CLASS_SET = 'gatewayClassSet';
    const GATEWAY_CLASS_P_REGEX_RECIPIENT = 'regexRecipient';

    /**
     * @param Job $job
     *
     * @return array
     * @throws WorkerException
     */
    protected function analiseGateway(Job $job)
    {
        $gatewayClassSet = self::getConfig(self::GATEWAY_CLASS_SET);

        if (count($gatewayClassSet) === 0) {
            throw new WorkerException('Empty gateway class set!');
        }

        $gatewayClassScore = [];
        foreach ($gatewayClassSet as $gatewayClass => $gatewayOptions) {
            $gatewayClassScore[$gatewayClass] = 0;

            if (preg_match($gatewayOptions[self::GATEWAY_CLASS_P_REGEX_RECIPIENT],
                $job->getRecipient())) {
                $gatewayClassScore[$gatewayClass]++;
            }

            // ......
        }

        $maxClass = array_keys($gatewayClassScore, max($gatewayClassScore))[0];

        if ($gatewayClassScore[$maxClass] === 0) {
            // Transport is bad : $maxClass = 0 , and we have no ideas what return :(
        }

        return $maxClass;
    }

    //endregion ***************************************************************

    // region Default Result Data**************************************************************

    const P_RESULT_DATA_VENDOR_NAME = 'vendor';
    const P_RESULT_DATA_VENDOR_ID = 'vendor_id';
    const P_RESULT_DATA_SUCCESS = 'success';
    const P_RESULT_DATA_ERRORS = 'errors';

    /**
     * @param null $vendor_name
     * @param null $vendor_id
     * @param null $success
     * @param null $errors
     *
     * @return array
     */
    public static function buildResultDataDefault(
        $vendor_name = null,
        $vendor_id = null,
        $success = null,
        $errors = null
    ) {
        return [
            self::P_RESULT_DATA_VENDOR_NAME => $vendor_name,
            self::P_RESULT_DATA_VENDOR_ID   => $vendor_id,
            self::P_RESULT_DATA_SUCCESS     => $success,
            self::P_RESULT_DATA_ERRORS      => $errors,
        ];
    }

    //endregion ***************************************************************

    /**
     * if job is sync then we get worker and try execute job
     * else if job is Async then redirect to other queue
     *
     * REMEMBER: Queue name is equal to Worker class name (through all "Cronario")
     *
     * @param AbstractJob|Job $job
     *
     * @throws ResultException
     * @throws WorkerException
     */
    protected function doJob(AbstractJob $job)
    {
        $gatewayClass = $this->analiseGateway($job);

        if ($job->isSync()) {
            $worker = AbstractWorker::factory($gatewayClass);

            return $worker($job);
        }

        if (!$job->hasAttempt()) {
            throw new ResultException(ResultException::FAILURE_MAX_ATTEMPTS);
        }

        // redirect result for new gateway class
        $job->addDebugData('set_gateway', $gatewayClass);
        $job->setWorkerClass($gatewayClass)->save();

        throw new ResultException(ResultException::REDIRECT_GATEWAY_CLASS);
    }
}