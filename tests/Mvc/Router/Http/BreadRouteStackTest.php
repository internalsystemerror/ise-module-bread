<?php

namespace IseBreadTest\Mvc\Router\Http;

use IseBread\Mvc\Router\Http\BreadRouteStack;

class BreadRouteStackTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var BreadRouteStack
     */
    protected $object;

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->object = new BreadRouteStack;
    }

    /**
     * @covers IseBread\Mvc\Router\Http\BreadRouteStack::factory
     * @todo   Implement testFactory().
     */
    public function testFactory()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
