<?php

namespace Messenger\Test;


class DummyTest extends \PHPUnit_Framework_TestCase
{

    public function testTrue()
    {
        $this->assertTrue(!!true);
        $this->assertTrue((bool) 1);
        $this->assertTrue((bool) 'a');
    }

}
