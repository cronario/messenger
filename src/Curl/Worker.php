<?php

namespace Messenger\Curl;

use Cronario\AbstractJob;
use Cronario\AbstractWorker;
use Cronario\Logger;
use Messenger\CurlWrapper;
use Messenger\CurlWrapperCurlException;
use Messenger\CurlWrapperException;

/**
 * Class Worker
 *
 * @package Messenger\Curl
 */
class Worker extends AbstractWorker
{


    // region HELPERS ********************************************************

    /**
     * @param $what
     * @param $where
     *
     * @return bool
     */
    protected function isContain($what, $where)
    {
        if (empty($what)) {
            return true;
        }

        if (empty($where)) {
            return false;
        }

        if (strpos((string) $where, (string) $what) !== false) {
            return true;
        }

        return false;
    }

    // endregion *************************************************************

    /**
     * @param AbstractJob $job
     *
     * @throws ResultException
     */
    protected function doJob(AbstractJob $job)
    {
        /** @var $job Job */
        $resultData = null;

        try {
            $curl = new CurlWrapper();
            $curl->request($job->getUrl(), $job->getMethod());
            $response['content'] = $curl->getResponse();
            $response['info'] = $curl->getTransferInfo();

        } catch (CurlWrapperCurlException $a) {
            $job->addDebugData('CurlWrapperCurlException', $a->getMessage());
            throw new ResultException(ResultException::ERROR_CURL);
        } catch (CurlWrapperException $a) {
            $job->addDebugData('CurlWrapperCurlException', $a->getMessage());
            throw new ResultException(ResultException::ERROR_CURL);
        } catch (\Exception $a) {
            $job->addDebugData('CurlWrapperCurlException', $a->getMessage());
            throw new ResultException(ResultException::ERROR_CURL);
        }

        /**
         * saving data
         */
        if ($job->getSaveContent()) {
            $resultData['content'] = $response['content'];
        }

        if ($job->getSaveInfo()) {
            $resultData['info'] = $response['info'];
        }

        /**
         * analise response
         */
        if ($job->getExpectCode() && $job->getExpectCode() != $response['info']['http_code']) {
            throw new ResultException(ResultException::FAILURE_EXPECTED_HTTP_CODE, $resultData);

        } elseif ($job->getExpectContent() && !$this->isContain($job->getExpectContent(), $response['content'])) {
            throw new ResultException(ResultException::FAILURE_EXPECTED_CONTENT, $resultData);
        }

        /**
         * success result
         */
        throw new ResultException(ResultException::R_SUCCESS, $resultData);
    }

}