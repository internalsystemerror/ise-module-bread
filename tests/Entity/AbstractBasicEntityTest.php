<?php

namespace IseBreadTest\Entity;

use IseBread\Entity\AbstractBasicEntity;

class AbstractBasicEntityTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var AbstractBasicEntity
     */
    protected $object;

    /**
     * Sets up the fixture
     */
    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass('IseBread\Entity\AbstractBasicEntity');
    }

    /**
     * @covers IseBread\Entity\AbstractBasicEntity::__toString
     * @todo   Implement testToString().
     */
    public function testToString()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers IseBread\Entity\AbstractBasicEntity::setName
     * @todo   Implement testSetName().
     */
    public function testSetName()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers IseBread\Entity\AbstractBasicEntity::getName
     * @todo   Implement testGetName().
     */
    public function testGetName()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers IseBread\Entity\AbstractBasicEntity::setDescription
     * @todo   Implement testSetDescription().
     */
    public function testSetDescription()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers IseBread\Entity\AbstractBasicEntity::getDescription
     * @todo   Implement testGetDescription().
     */
    public function testGetDescription()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
