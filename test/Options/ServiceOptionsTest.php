<?php

namespace IseTest\Bread\Options;

use Ise\Bread\Options\ServiceOptions;

class ServiceOptionsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var AbstractActionController
     */
    protected $object;

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->object = new ServiceOptions;
    }
}
