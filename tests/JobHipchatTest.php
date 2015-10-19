<?php

namespace Messenger\Test;

use \Cronario\Facade;
use \Cronario\Producer;


class mockHipchat
{
    public function __construct($token)
    {

    }

    public function message_room()
    {
        return true;
    }
}


class JobHipchatTest extends \PHPUnit_Framework_TestCase
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

    public function createJobSkeleton()
    {
        return new \Messenger\Hipchat\Job([
            \Messenger\Hipchat\Job::P_APP_ID  => self::TEST_PRODUCER_MESSENGER_ID,
            \Messenger\Hipchat\Job::P_IS_SYNC => true,
        ]);
    }


    public function testCreateJob()
    {
        $hipchat = $this->createJobSkeleton();
        $hipchat->setToken('token-xxx-yyy');
        $hipchat->setFrom('Boss');
        $hipchat->setRoom('my-sweet-room');
        $hipchat->setFormat(\Messenger\Hipchat\Job::P_PARAM_FORMAT_T_TEXT);
        $hipchat->setColour(\Messenger\Hipchat\Job::P_PARAM_COLOUR_T_RED);
        $hipchat->setMsg('Test my msg like a boss!');

        $this->assertInstanceOf('\\Messenger\\Hipchat\\Job', $hipchat);
        $this->assertInstanceOf('\\Cronario\\AbstractJob', $hipchat);

        $this->assertEquals('token-xxx-yyy', $hipchat->getToken());
        $this->assertEquals('Boss', $hipchat->getFrom());
        $this->assertEquals('my-sweet-room', $hipchat->getRoom());
        $this->assertEquals(\Messenger\Hipchat\Job::P_PARAM_FORMAT_T_TEXT, $hipchat->getFormat());
        $this->assertEquals(\Messenger\Hipchat\Job::P_PARAM_COLOUR_T_RED, $hipchat->getColour());
        $this->assertEquals('Test my msg like a boss!', $hipchat->getMsg());
    }


    public function testDoJob()
    {
        $mockHipchat = new mockHipchat('xxx');

        /** @var \Messenger\Hipchat\Worker $workerHipchat */
        $workerHipchat = $this->getMock('\Messenger\Hipchat\Worker', array('getTransport'));
        $workerHipchat
            ->method('getTransport')
            ->will($this->returnValue($mockHipchat));


        $hipchat = $this->createJobSkeleton();
        $hipchat->setToken('token-xxx-yyy');
        $hipchat->setFrom('Boss');
        $hipchat->setRoom('my-sweet-room');
        $hipchat->setFormat(\Messenger\Hipchat\Job::P_PARAM_FORMAT_T_TEXT);
        $hipchat->setColour(\Messenger\Hipchat\Job::P_PARAM_COLOUR_T_RED);
        $hipchat->setMsg('Test my msg like a boss!');

        $result = $workerHipchat($hipchat);
        $resultArray = $result->toArray();

        $this->assertInternalType('array', $resultArray);
        $this->assertArrayHasKey('globalCode', $resultArray);
        $this->assertArrayHasKey('message', $resultArray);
    }


    public function testWorkerGetTransport()
    {

        $worker = new \Messenger\Hipchat\Worker();
        $hipchat = $worker->getTransport('token');

        $this->assertInstanceOf('\\HipChat\\HipChat', $hipchat);
    }


    public function testDoJobFailValidateParams()
    {
        $hipchat = $this->createJobSkeleton();
//        $hipchat->setToken('token-xxx-yyy');
        $hipchat->setRoom('my-sweet-room');
//        $hipchat->setFrom('Boss');
//        $hipchat->setMsg('Test my msg like a boss!');

        $result = $hipchat();
        $this->assertGreaterThan(0, $result->getGlobalCode());

        // ============================

        $hipchat = $this->createJobSkeleton();
        $hipchat->setToken('token-xxx-yyy');
//        $hipchat->setRoom('my-sweet-room');
//        $hipchat->setFrom('Boss');
//        $hipchat->setMsg('Test my msg like a boss!');

        $result = $hipchat();
        $this->assertGreaterThan(0, $result->getGlobalCode());

        // ============================

        $hipchat = $this->createJobSkeleton();
        $hipchat->setToken('token-xxx-yyy');
        $hipchat->setRoom('my-sweet-room');
//        $hipchat->setFrom('Boss');
//        $hipchat->setMsg('Test my msg like a boss!');

        $result = $hipchat();
        $this->assertGreaterThan(0, $result->getGlobalCode());

        // ============================

        $hipchat = $this->createJobSkeleton();
        $hipchat->setToken('token-xxx-yyy');
        $hipchat->setRoom('my-sweet-room');
        $hipchat->setFrom('Boss');
//        $hipchat->setMsg('Test my msg like a boss!');

        $result = $hipchat();
        $this->assertGreaterThan(0, $result->getGlobalCode());

    }

}
