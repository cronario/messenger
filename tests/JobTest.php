<?php


namespace Messenger\Test;


use Messenger\Curl\Job;

class JobTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Messenger\Curl\Job
     */
    private $job;

    public function setUp()
    {
        $this->job = new Job();
    }

    public function testGetAndSet()
    {
        $this->job->setUrl('https://google.com');
        $this->assertEquals('https://google.com', $this->job->getUrl());

        $this->job->setMethod('POST');
        $this->assertEquals('POST', $this->job->getMethod());

        $this->job->setExpectContent('feeling lucky');
        $this->assertEquals('feeling lucky', $this->job->getExpectContent());

        $this->job->setExpectCode('200');
        $this->assertEquals('200', $this->job->getExpectCode());

        $this->job->setSaveContent(true);
        $this->assertTrue($this->job->getSaveContent());

        $this->job->setSaveInfo(true);
        $this->assertTrue($this->job->getSaveInfo());
    }
}
