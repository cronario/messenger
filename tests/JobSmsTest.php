<?php

namespace Messenger\Test;

class mockAlphaSmsClient
{

    public function __call($method, $args)
    {

    }

    public static function __callStatic($method, $args)
    {

    }

    public function getResponse()
    {
        return [
            'errors' => null,
            'id'     => 0,
        ];
    }

}

class mockInfobipClient
{

    public function __call($method, $args)
    {
    }

    public static function __callStatic($method, $args)
    {
    }

    public function getResponse()
    {
        return [
            'errors' => null,
            'id'     => 0,
        ];
    }

}

class JobSmsTest extends \PHPUnit_Framework_TestCase
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

        \Cronario\Facade::addProducer(new \Cronario\Producer([
            \Cronario\Producer::P_APP_ID => self::TEST_PRODUCER_MESSENGER_ID
        ]));
    }

    public function tearDown()
    {
        \Cronario\Facade::cleanProducers();
    }


    public function createJobSkeleton()
    {
        return new \Messenger\Sms\Job([
            \Messenger\Sms\Job::P_APP_ID  => self::TEST_PRODUCER_MESSENGER_ID,
            \Messenger\Sms\Job::P_IS_SYNC => true,
        ]);

    }

    public function testCreateJob()
    {
        $sms = $this->createJobSkeleton();
        $sms->setRecipient('380670000000');
        $sms->setSender('sender');
        $sms->setText('text');

        $this->assertInstanceOf('\\Messenger\\Sms\\Job', $sms);
        $this->assertInstanceOf('\\Cronario\\AbstractJob', $sms);
        $this->assertEquals('380670000000', $sms->getRecipient());
        $this->assertEquals('sender', $sms->getSender());
        $this->assertEquals('text', $sms->getText());
    }

    public function testDoJobByAlphaSms()
    {
        $sms = $this->createJobSkeleton();
        $sms->setRecipient('380670000000');
        $sms->setSender('sender');
        $sms->setText('text');

        $mockAlphaSmsClient = new mockAlphaSmsClient();

        /** @var \Messenger\Sms\Alpha\Worker $worker */
        // $worker = new \Messenger\Sms\Alpha\Worker();
        $worker = $this->getMock('\\Messenger\\Sms\\Alpha\\Worker', ['getTransport']);
        $worker
            ->method('getTransport')
            ->will($this->returnValue($mockAlphaSmsClient));

        $result = $worker($sms);
        $resultArray = $result->toArray();

        $this->assertInternalType('array', $resultArray);
        $this->assertArrayHasKey('globalCode', $resultArray);
        $this->assertArrayHasKey('message', $resultArray);
    }

    public function testDoJobAlphaWorkerForce()
    {
        $sms = $this->createJobSkeleton();
        $sms->setRecipient('380670000000');
        $sms->setSender('sender');
        $sms->setText('text');

        $worker = new \Messenger\Sms\Alpha\Worker();

        $result = $worker($sms);
        $resultArray = $result->toArray();

        $this->assertInternalType('array', $resultArray);
        $this->assertArrayHasKey('globalCode', $resultArray);
        $this->assertArrayHasKey('message', $resultArray);

    }


    public function testDoJobByInfobip()
    {
        $sms = $this->createJobSkeleton();
        $sms->setRecipient('380670000000');
        $sms->setSender('sender');
        $sms->setText('text');

        $mockInfobipClient = new mockInfobipClient();

        /** @var \Messenger\Sms\Infobip\Worker $worker */
        // $worker = new \Messenger\Sms\Infobip\Worker();
        $worker = $this->getMock('\\Messenger\\Sms\\Infobip\\Worker', ['getTransport']);
        $worker
            ->method('getTransport')
            ->will($this->returnValue($mockInfobipClient));

        $result = $worker($sms);
        $resultArray = $result->toArray();

        $this->assertInternalType('array', $resultArray);
        $this->assertArrayHasKey('globalCode', $resultArray);
        $this->assertArrayHasKey('message', $resultArray);
    }

    public function testDoJobInfobipWorkerForce()
    {
        $sms = $this->createJobSkeleton();
        $sms->setRecipient('380670000000');
        $sms->setSender('sender');
        $sms->setText('text');

        $worker = new \Messenger\Sms\Infobip\Worker();

        $result = $worker($sms);
        $resultArray = $result->toArray();

        $this->assertInternalType('array', $resultArray);
        $this->assertArrayHasKey('globalCode', $resultArray);
        $this->assertArrayHasKey('message', $resultArray);

    }

    public function testDoJobValidateFail()
    {

        $sms = $this->createJobSkeleton();
        $sms->setWorkerClass('\\Messenger\\Sms\\Alpha\\Worker');

        $sms->setSender('sender');
        // $sms->setRecipient('380670000000');
        // $sms->setText('text');

        $result = $sms();

        $this->assertGreaterThan(0, $result->getGlobalCode());

        // ===============================


        $sms = $this->createJobSkeleton();
        $sms->setWorkerClass('\\Messenger\\Sms\\Alpha\\Worker');

        // $sms->setSender('sender');
        $sms->setRecipient('380670000000');
        // $sms->setText('text');

        $result = $sms();

        $this->assertGreaterThan(0, $result->getGlobalCode());

        // ===============================


        $sms = $this->createJobSkeleton();
        $sms->setWorkerClass('\\Messenger\\Sms\\Alpha\\Worker');

        // $sms->setSender('sender');
        // $sms->setRecipient('380670000000');
        $sms->setText('text');

        $result = $sms();

        $this->assertGreaterThan(0, $result->getGlobalCode());

        // ===============================

        $sms = $this->createJobSkeleton();
        $sms->setWorkerClass('\\Messenger\\Sms\\Alpha\\Worker');

        $sms->setSender('sender');
        $sms->setRecipient('380670000000');
        // $sms->setText('text');

        $result = $sms();

        $this->assertGreaterThan(0, $result->getGlobalCode());

        // ===============================

    }


    public function testDoJobDispatch()
    {
        $sms = $this->createJobSkeleton();
        $sms->setRecipient('380670000000');
        $sms->setSender('sender');
        $sms->setText('text');

        $worker = new \Messenger\Sms\Worker();

        $result = $worker($sms);
        $resultArray = $result->toArray();

        $this->assertInternalType('array', $resultArray);
        $this->assertArrayHasKey('globalCode', $resultArray);
        $this->assertArrayHasKey('message', $resultArray);

    }


    public function testDoJobDispatchAsync()
    {
        $sms = $this->createJobSkeleton();
        $sms->setSync(false);
        $sms->setRecipient('380670000000');
        $sms->setSender('sender');
        $sms->setText('text');

        $worker = new \Messenger\Sms\Worker();

        $result = $worker($sms);
        $resultArray = $result->toArray();

        $this->assertInternalType('array', $resultArray);
        $this->assertArrayHasKey('globalCode', $resultArray);
        $this->assertArrayHasKey('message', $resultArray);

    }


    public function testDoJobDispatchAsyncMaxAttempts()
    {
        $sms = $this->createJobSkeleton();
        $sms->setSync(false);
        $sms->setAttemptsMax(5);
        $sms->setAttempts(5);
        $sms->setRecipient('380670000000');
        $sms->setSender('sender');
        $sms->setText('text');

        $worker = new \Messenger\Sms\Worker();

        $result = $worker($sms);
        $resultArray = $result->toArray();

        $this->assertInternalType('array', $resultArray);
        $this->assertArrayHasKey('globalCode', $resultArray);
        $this->assertArrayHasKey('message', $resultArray);

    }
}
