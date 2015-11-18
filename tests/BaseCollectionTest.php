<?php

namespace ProtobufTest;

use Protobuf\BaseCollection;

class BaseCollectionTest extends TestCase
{
    /**
     * @var \Protobuf\BaseCollection
     */
    protected $collection;

    protected function setUp()
    {
        $this->collection = $this->getMockBuilder(BaseCollection::CLASS)
            ->setMethods(['__construct'])
            ->getMock();
    }

    public function testArrayAccess()
    {
        $this->assertCount(0, $this->collection);
        $this->assertTrue($this->collection->isEmpty());

        $this->collection[] = 1;
        $this->collection[] = 2;
        $this->collection[] = 3;

        $this->assertCount(3, $this->collection);
        $this->assertArrayHasKey(0, $this->collection);
        $this->assertArrayHasKey(1, $this->collection);
        $this->assertArrayHasKey(2, $this->collection);
        $this->assertFalse($this->collection->isEmpty());
        $this->assertEquals([1, 2, 3], $this->collection->getValues());

        $this->collection[0] = 11;
        $this->collection[1] = 22;
        $this->collection[2] = 33;

        $this->assertCount(3, $this->collection);
        $this->assertEquals([11, 22, 33], $this->collection->getValues());

        unset($this->collection[1]);

        $this->assertCount(2, $this->collection);
        $this->assertArrayHasKey(0, $this->collection);
        $this->assertArrayHasKey(2, $this->collection);
    }

    public function testIteratorAggregate()
    {
        $this->assertCount(0, $this->collection);

        $this->collection[] = 1;
        $this->collection[] = 2;
        $this->collection[] = 3;

        $this->assertCount(3, $this->collection);

        $iterator = $this->collection->getIterator();
        $values   = iterator_to_array($iterator);

        $this->assertEquals([1, 2, 3], $values);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid NULL element
     */
    public function testInvalidArgumentExceptionOffsetSet()
    {
        $this->assertCount(0, $this->collection);
        $this->assertTrue($this->collection->isEmpty());

        $this->collection[] = null;
    }

    /**
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage Undefined index 'UNKNOWN'
     */
    public function testOutOfBoundsExceptionOffsetGet()
    {
        $this->assertCount(0, $this->collection);
        $this->assertTrue($this->collection->isEmpty());

        $this->collection['UNKNOWN'];
    }

    /**
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage Undefined index 'UNKNOWN'
     */
    public function testOutOfBoundsExceptionOffsetUnset()
    {
        $this->assertCount(0, $this->collection);
        $this->assertTrue($this->collection->isEmpty());

        unset($this->collection['UNKNOWN']);
    }
}
