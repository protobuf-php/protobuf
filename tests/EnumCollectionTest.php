<?php

namespace ProtobufTest;

use Protobuf\EnumCollection;
use Protobuf\Enum;

class EnumCollectionTest extends TestCase
{
    /**
     * @var \Protobuf\EnumCollection
     */
    protected $collection;

    protected function setUp()
    {
        $this->collection = new EnumCollection();
    }

    public function testCreateEnumCollection()
    {
        $enum1 = $this->getMock('Protobuf\Enum', [], ['E1', 1]);
        $enum2 = $this->getMock('Protobuf\Enum', [], ['E2', 2]);

        $collection = new EnumCollection([$enum1, $enum2]);

        $this->assertCount(2, $collection);
        $this->assertEquals([$enum1, $enum2], $collection->getArrayCopy());
    }

    public function testAddEnum()
    {
        $this->assertCount(0, $this->collection);

        $enum1 = $this->getMock('Protobuf\Enum', [], ['E1', 1]);
        $enum2 = $this->getMock('Protobuf\Enum', [], ['E2', 2]);

        $this->collection[] = $enum1;

        $this->collection->add($enum2);

        $this->assertCount(2, $this->collection);
        $this->assertEquals([$enum1, $enum2], $this->collection->getArrayCopy());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Argument 2 passed to Protobuf\EnumCollection::offsetSet must be a \Protobuf\Enum, stdClass given
     */
    public function testInvalidArgumentExceptionOffsetSetObject()
    {
        $this->collection[] = new \stdClass();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Argument 2 passed to Protobuf\EnumCollection::offsetSet must be a \Protobuf\Enum, integer given
     */
    public function testInvalidArgumentExceptionOffsetSetInteger()
    {
        $this->collection[] = 123;
    }
}
