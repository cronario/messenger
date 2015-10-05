<?php

namespace Messenger\Test;

use \Cronario\Facade;
use \Cronario\Producer;


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

    public function testCreateJob()
    {
        $mail = new \Messenger\Mail\Job([
            \Messenger\Curl\Job::P_APP_ID => self::TEST_PRODUCER_MESSENGER_ID
        ]);

        $mail->setFromName('Mail Boss');
        $mail->setFromMail('boss@example.com');
        $mail->setSubject('Big boss says!');
        $mail->setToMail('worker@example.com');
        $mail->setBody('text text text ...');
        $mail->setAttachment([
            [
                \Messenger\Mail\Job::P_PARAM_ATTACHMENT__PATH        => '/folder/big-data-file.csv',
                \Messenger\Mail\Job::P_PARAM_ATTACHMENT__NAME        => 'file',
                \Messenger\Mail\Job::P_PARAM_ATTACHMENT__TYPE        => 'text/csv',
                \Messenger\Mail\Job::P_PARAM_ATTACHMENT__DISPOSITION => 'big data file',
                \Messenger\Mail\Job::P_PARAM_ATTACHMENT__ENCODING    => 'UTF-8',
                \Messenger\Mail\Job::P_PARAM_ATTACHMENT__ID          => 'att-id-123',
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
        $this->assertTrue(!!true);
    }

}
