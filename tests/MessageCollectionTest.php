<?php

namespace ProtobufTest;

use Protobuf\MessageCollection;
use Protobuf\Message;

class MessageCollectionTest extends TestCase
{
    /**
     * @var \Protobuf\MessageCollection
     */
    protected $collection;

    protected function setUp()
    {
        $this->collection = new MessageCollection();
    }

    public function testCreateMessageCollection()
    {
        $messge1 = $this->getMock(Message::CLASS);
        $messge2 = $this->getMock(Message::CLASS);

        $collection = new MessageCollection([$messge1, $messge2]);

        $this->assertCount(2, $collection);
        $this->assertEquals([$messge1, $messge2], $collection->getArrayCopy());
    }

    public function testAddMessage()
    {
        $this->assertCount(0, $this->collection);

        $messge1 = $this->getMock(Message::CLASS);
        $messge2 = $this->getMock(Message::CLASS);

        $this->collection[] = $messge1;

        $this->collection->add($messge2);

        $this->assertCount(2, $this->collection);
        $this->assertEquals([$messge1, $messge2], $this->collection->getArrayCopy());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Argument 2 passed to Protobuf\MessageCollection::offsetSet must implement interface \Protobuf\Message, stdClass given
     */
    public function testInvalidArgumentExceptionOffsetSetObject()
    {
        $this->collection[] = new \stdClass();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Argument 2 passed to Protobuf\MessageCollection::offsetSet must implement interface \Protobuf\Message, integer given
     */
    public function testInvalidArgumentExceptionOffsetSetInteger()
    {
        $this->collection[] = 123;
    }
}
