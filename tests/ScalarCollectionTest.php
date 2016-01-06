<?php

namespace ProtobufTest;

use Protobuf\ScalarCollection;

class ScalarCollectionTest extends TestCase
{
    /**
     * @var \Protobuf\ScalarCollection
     */
    protected $collection;

    protected function setUp()
    {
        $this->collection = new ScalarCollection();
    }

    public function testCreateScalarCollection()
    {
        $collection = new ScalarCollection([1,2]);

        $this->assertCount(2, $collection);
        $this->assertEquals([1, 2], $collection->getArrayCopy());
    }

    public function testAddValue()
    {
        $this->assertCount(0, $this->collection);

        $this->collection[] = 1;

        $this->collection->add(2);

        $this->assertCount(2, $this->collection);
        $this->assertEquals([1, 2], $this->collection->getArrayCopy());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to Protobuf\ScalarCollection::add must be a scalar value, stdClass given
     */
    public function testInvalidArgumentExceptionAddObject()
    {
        $this->collection->add(new \stdClass());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Argument 2 passed to Protobuf\ScalarCollection::offsetSet must be a scalar value, stdClass given
     */
    public function testInvalidArgumentExceptionOffsetSetObject()
    {
        $this->collection[] = new \stdClass();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Argument 2 passed to Protobuf\ScalarCollection::offsetSet must be a scalar value, array given
     */
    public function testInvalidArgumentExceptionOffsetSetInteger()
    {
        $this->collection[] = [];
    }
}
