<?php

namespace Messenger\Test;

use \Cronario\Facade;
use \Cronario\Producer;

class JobCurlTest extends \PHPUnit_Framework_TestCase
{

    const TEST_PRODUCER_MESSENGER_ID = 'messenger-app-id';

    public function setUp()
    {
        \Result\ResultException::setClassIndexMap([
            'Cronario\\Exception\\ResultException' => 1,
            'Cronario\\Test\\ResultException'      => 2,
            'Messenger\\Curl\\ResultException'     => 3,
            'Messenger\\Mail\\ResultException'     => 4,
            'Messenger\\Sms\\ResultException'      => 5,
            'Messenger\\Hipchat\\ResultException'  => 6,
        ]);

        Facade::addProducer(new Producer([
            Producer::P_APP_ID => self::TEST_PRODUCER_MESSENGER_ID
        ]));
    }

    public function tearDown()
    {
        Facade::cleanProducers();
    }

    public function testCreateJob()
    {
        $curl = new \Messenger\Curl\Job([
            \Messenger\Curl\Job::P_APP_ID => self::TEST_PRODUCER_MESSENGER_ID
        ]);

        $curl->setUrl('https:://google.com');
        $curl->setMethod('get');
        $curl->setExpectCode(200);
        $curl->setExpectContent('google');
        $curl->setSaveContent(true);
        $curl->setSaveInfo(true);

        $this->assertInstanceOf('\\Messenger\\Curl\\Job', $curl);
        $this->assertInstanceOf('\\Cronario\\AbstractJob', $curl);

        $this->assertEquals('https:://google.com', $curl->getUrl());
        $this->assertEquals('get', $curl->getMethod());
        $this->assertEquals(200, $curl->getExpectCode());
        $this->assertEquals('google', $curl->getExpectContent());
        $this->assertTrue($curl->getSaveContent());
        $this->assertTrue($curl->getSaveInfo());

    }


    public function testDoJob()
    {

        $curl = new \Messenger\Curl\Job([
            \Messenger\Curl\Job::P_APP_ID => self::TEST_PRODUCER_MESSENGER_ID
        ]);

        $curl->setUrl('https://google.com');
        $curl->setMethod('get');
        $curl->setExpectCode(200);
        $curl->setExpectContent('google');
        $curl->setSaveContent(true);
        $curl->setSaveInfo(true);
        $curl->setSync(true);

        $result = $curl();
        $resultArray = $result->toArray();

        $this->assertInternalType('array', $resultArray);
        $this->assertArrayHasKey('globalCode', $resultArray);
        $this->assertArrayHasKey('message', $resultArray);
        $this->assertArrayHasKey('data', $resultArray);
        $this->assertArrayHasKey('content', $resultArray['data']);
        $this->assertArrayHasKey('info', $resultArray['data']);
    }

}
