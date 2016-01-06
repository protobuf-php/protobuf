<?php

namespace ProtobufTest;

use Protobuf\StreamCollection;
use Protobuf\Stream;

class StreamCollectionTest extends TestCase
{
    /**
     * @var \Protobuf\StreamCollection
     */
    protected $collection;

    protected function setUp()
    {
        $this->collection = new StreamCollection();
    }

    public function testCreateStreamCollection()
    {
        $stream1 = Stream::create();
        $stream2 = Stream::create();

        $collection = new StreamCollection([$stream1, $stream2]);

        $this->assertCount(2, $collection);
        $this->assertEquals([$stream1, $stream2], $collection->getArrayCopy());
    }

    public function testAddStream()
    {
        $this->assertCount(0, $this->collection);

        $stream1 = Stream::create();
        $stream2 = Stream::create();

        $this->collection[] = $stream1;

        $this->collection->add($stream2);

        $this->assertCount(2, $this->collection);
        $this->assertEquals([$stream1, $stream2], $this->collection->getArrayCopy());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Argument 2 passed to Protobuf\StreamCollection::offsetSet must be a \Protobuf\Stream, stdClass given
     */
    public function testInvalidArgumentExceptionOffsetSetObject()
    {
        $this->collection[] = new \stdClass();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Argument 2 passed to Protobuf\StreamCollection::offsetSet must be a \Protobuf\Stream, integer given
     */
    public function testInvalidArgumentExceptionOffsetSetInteger()
    {
        $this->collection[] = 123;
    }
}
