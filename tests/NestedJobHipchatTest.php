<?php

namespace Messenger\Test;



class ExampleJobTemplate extends \Messenger\Hipchat\Job
{
    const P_PARAM_TEMPLATE_CONFIG = 'config';
    const P_PARAM_TEMPLATE_CONFIG_T_TEMPLATE = 'template';
    const P_PARAM_TEMPLATE_CONFIG_T_VARS = 'vars';
    const P_PARAM_TEMPLATE_CONFIG_T_LOCALE = 'locale';

    public function getWorkerClass()
    {
        /**
         * if we want use real worker for this job
         * or you can put yours custom worker
         */
        return \Messenger\Hipchat\Worker::getClassPath();
    }

    public function getTemplateConfig()
    {
        return $this->getParam(self::P_PARAM_TEMPLATE_CONFIG);
    }

    public function setTemplateConfig(array $config = [])
    {
        return $this->setParam(self::P_PARAM_TEMPLATE_CONFIG, $config);
    }

    protected $templateData;

    public function getTemplateValue($key)
    {
        /**
         * Yours custom logic goes here ....
         */

        if (null === $this->templateData) {
            $this->templateData = [];
            $config = $this->getTemplateConfig();
            if ('custom-template' == $config[self::P_PARAM_TEMPLATE_CONFIG_T_TEMPLATE]) {

                $template = $config[self::P_PARAM_TEMPLATE_CONFIG_T_TEMPLATE];
                $lang = $config[self::P_PARAM_TEMPLATE_CONFIG_T_LOCALE];
                $vars = $config[self::P_PARAM_TEMPLATE_CONFIG_T_VARS];

                $this->templateData = [
                    self::P_PARAM_TOKEN  => 'custom-token',
                    self::P_PARAM_FROM   => 'custom-from',
                    self::P_PARAM_ROOM   => 'custom-room',
                    self::P_PARAM_FORMAT => 'custom-format',
                    self::P_PARAM_COLOUR => 'custom-colour',
                    self::P_PARAM_MSG    => 'custom-msg',
                ];
            }
        }

        return array_key_exists($key, $this->templateData) ? $this->templateData[$key] : null;
    }


    public function getToken()
    {
        /**
         * if we not define real param 'token'
         * then we will try get default 'token' from template Value ...
         */
        return $this->getParam(self::P_PARAM_TOKEN, $this->getTemplateValue(self::P_PARAM_TOKEN));
    }

    public function getFrom()
    {
        return $this->getParam(self::P_PARAM_FROM, $this->getTemplateValue(self::P_PARAM_FROM));
    }

    public function getRoom()
    {
        return $this->getParam(self::P_PARAM_ROOM, $this->getTemplateValue(self::P_PARAM_ROOM));
    }

    public function getFormat()
    {
        return $this->getParam(self::P_PARAM_FORMAT, $this->getTemplateValue(self::P_PARAM_FORMAT));
    }

    public function getColour()
    {
        return $this->getParam(self::P_PARAM_COLOUR, $this->getTemplateValue(self::P_PARAM_COLOUR));
    }

    public function getMsg()
    {
        return $this->getParam(self::P_PARAM_MSG, $this->getTemplateValue(self::P_PARAM_MSG));
    }
}

class NestedJobHipchatTest extends \PHPUnit_Framework_TestCase
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
        return new ExampleJobTemplate([
            ExampleJobTemplate::P_APP_ID  => self::TEST_PRODUCER_MESSENGER_ID,
            ExampleJobTemplate::P_IS_SYNC => true,
        ]);
    }


    public function testCreateJob()
    {
        $hipchat = new ExampleJobTemplate([
            ExampleJobTemplate::P_APP_ID  => self::TEST_PRODUCER_MESSENGER_ID,
            ExampleJobTemplate::P_IS_SYNC => true,
            ExampleJobTemplate::P_PARAMS  => [
                ExampleJobTemplate::P_PARAM_TEMPLATE_CONFIG => [
                    'template' => 'custom-template',
                    'locale'   => 'en',
                    'vars'     => [
                        'key-1' => 'value-1',
                        'key-2' => 'value-2',
                        'key-3' => 'value-3',
                    ],
                ],
            ]
        ]);

        $this->assertInstanceOf('\\Messenger\\Hipchat\\Job', $hipchat);
        $this->assertInstanceOf('\\Cronario\\AbstractJob', $hipchat);

        $this->assertInternalType('array', $hipchat->getTemplateConfig());
        $this->assertArrayHasKey('template', $hipchat->getTemplateConfig());
        $this->assertArrayHasKey('vars', $hipchat->getTemplateConfig());

        $this->assertEquals('custom-token', $hipchat->getToken());
        $this->assertEquals('custom-from', $hipchat->getFrom());
        $this->assertEquals('custom-room', $hipchat->getRoom());
        $this->assertEquals('custom-format', $hipchat->getFormat());
        $this->assertEquals('custom-colour', $hipchat->getColour());
        $this->assertEquals('custom-msg', $hipchat->getMsg());
        $this->assertEquals('\\Messenger\\Hipchat\\Worker', $hipchat->getWorkerClass());
    }


    public function testCreateBySetters()
    {
        $hipchat = new ExampleJobTemplate();
        $hipchat->setAppId(self::TEST_PRODUCER_MESSENGER_ID);
        $hipchat->setSync(true);
        $hipchat->setTemplateConfig([
            'template' => 'custom-template',
            'locale'   => 'en',
            'vars'     => [
                'key-1' => 'value-1',
                'key-2' => 'value-2',
                'key-3' => 'value-3',
            ],
        ]);

        $this->assertInstanceOf('\\Messenger\\Hipchat\\Job', $hipchat);
        $this->assertInstanceOf('\\Cronario\\AbstractJob', $hipchat);

        $this->assertInternalType('array', $hipchat->getTemplateConfig());
        $this->assertArrayHasKey('template', $hipchat->getTemplateConfig());
        $this->assertArrayHasKey('vars', $hipchat->getTemplateConfig());

        $this->assertEquals('custom-token', $hipchat->getToken());
        $this->assertEquals('custom-from', $hipchat->getFrom());
        $this->assertEquals('custom-room', $hipchat->getRoom());
        $this->assertEquals('custom-format', $hipchat->getFormat());
        $this->assertEquals('custom-colour', $hipchat->getColour());
        $this->assertEquals('custom-msg', $hipchat->getMsg());
        $this->assertEquals('\\Messenger\\Hipchat\\Worker', $hipchat->getWorkerClass());
    }

}
