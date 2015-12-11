<?php

namespace Messenger\Test;

use Cronario\AbstractWorker;
use \Cronario\Facade;
use \Cronario\Producer;

class mockPHPMailer
{

    public function __call($method, $args)
    {

    }

    public static function __callStatic($method, $args)
    {

    }

    public static function send()
    {
        return true;
    }

    public function createAttachment($arg)
    {
        return new \stdClass();
    }

}

class JobMailTest extends \PHPUnit_Framework_TestCase
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
        return new \Messenger\Mail\Job([
            \Messenger\Mail\Job::P_APP_ID  => self::TEST_PRODUCER_MESSENGER_ID,
            \Messenger\Mail\Job::P_IS_SYNC => true,
        ]);

    }


    public function testCreateJob()
    {
        $mail = $this->createJobSkeleton();
        $mail->setFromName('Mail Boss');
        $mail->setFromMail('boss@example.com');
        $mail->setSubject('Big boss says!');
        $mail->setToMail('worker@example.com');
        $mail->setBody('text text text ...');
        $mail->setAttachment([
            [
                \Messenger\Mail\Job::P_PARAM_ATTACHMENT__PATH        => '/folder/big-data-file.csv',
                \Messenger\Mail\Job::P_PARAM_ATTACHMENT__NAME        => 'file-super.csv',
            ]
        ]);

        $this->assertInstanceOf('\\Messenger\\Mail\\Job', $mail);
        $this->assertInstanceOf('\\Cronario\\AbstractJob', $mail);

        $this->assertEquals('Mail Boss', $mail->getFromName());
        $this->assertEquals('boss@example.com', $mail->getFromMail());
        $this->assertEquals('Big boss says!', $mail->getSubject());
        $this->assertEquals('worker@example.com', $mail->getToMail());
        $this->assertEquals('text text text ...', $mail->getBody());
        $this->assertInternalType('array', $mail->getAttachment());
    }


    public function testDoJob()
    {
        $mockMailObject = new mockPHPMailer('xxx');

        /** @var \Messenger\Mail\Worker $workerMail */
        $workerMail = $this->getMock('\Messenger\Mail\Worker', ['getMail']);
        $workerMail
            ->method('getMail')
            ->will($this->returnValue($mockMailObject));

        $mail = $this->createJobSkeleton();
        $mail->setFromName('Mail Boss');
        $mail->setFromMail('boss@example.com');
        $mail->setSubject('Big boss says!');
        $mail->setToMail('worker@example.com');
        $mail->setBody('text text text ...');
        $mail->setAttachment([
            [
                \Messenger\Mail\Job::P_PARAM_ATTACHMENT__PATH        => __FILE__,
                \Messenger\Mail\Job::P_PARAM_ATTACHMENT__NAME        => 'file.csv',
            ]
        ]);

        $result = $workerMail($mail);
        $resultArray = $result->toArray();
        $this->assertInternalType('array', $resultArray);
        $this->assertArrayHasKey('globalCode', $resultArray);
        $this->assertArrayHasKey('message', $resultArray);
    }


    public function testDoJobValidateFail()
    {

        $mail = $this->createJobSkeleton();
//        $mail->setFromMail('boss@example.com');
//        $mail->setFromName('Mail Boss');
//        $mail->setToMail('worker@example.com');
//        $mail->setSubject('Big boss says!');
        $mail->setBody('text text text ...');

        $result = $mail();

        $this->assertGreaterThan(0, $result->getGlobalCode());

        // ==========================


        $mail = $this->createJobSkeleton();
        $mail->setFromMail('boss@example.com');
//        $mail->setFromName('Mail Boss');
//        $mail->setToMail('worker@example.com');
//        $mail->setSubject('Big boss says!');
//        $mail->setBody('text text text ...');

        $result = $mail();

        $this->assertGreaterThan(0, $result->getGlobalCode());

        // ==========================

        $mail = $this->createJobSkeleton();
        $mail->setFromMail('boss@example.com');
        $mail->setFromName('Mail Boss');
//        $mail->setToMail('worker@example.com');
//        $mail->setSubject('Big boss says!');
//        $mail->setBody('text text text ...');

        $result = $mail();

        $this->assertGreaterThan(0, $result->getGlobalCode());

        // ==========================

        $mail = $this->createJobSkeleton();
        $mail->setFromMail('boss@example.com');
        $mail->setFromName('Mail Boss');
        $mail->setToMail('worker@example.com');
//        $mail->setSubject('Big boss says!');
//        $mail->setBody('text text text ...');

        $result = $mail();

        $this->assertGreaterThan(0, $result->getGlobalCode());

        // ==========================

        $mail = $this->createJobSkeleton();
        $mail->setFromMail('boss@example.com');
        $mail->setFromName('Mail Boss');
        $mail->setToMail('worker@example.com');
        $mail->setSubject('Big boss says!');
//        $mail->setBody('text text text ...');

        $result = $mail();

        $this->assertGreaterThan(0, $result->getGlobalCode());

    }

}
