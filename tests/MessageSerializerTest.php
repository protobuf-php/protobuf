<?php

namespace ProtobufTest;

use ProtobufTest\Protos\Simple;

use Protobuf\MessageSerializer;
use Protobuf\Configuration;
use Protobuf\Message;
use Protobuf\Stream;

class MessageSerializerTest extends TestCase
{
    public function testSerializeMessage()
    {
        $message    = $this->getMock(Message::CLASS);
        $config     = new Configuration();
        $serializer = new MessageSerializer($config);
        $stream     = Stream::create();

        $message->expects($this->once())
            ->method('toStream')
            ->willReturn($stream)
            ->with($this->equalTo($config));

        $this->assertInstanceOf('Protobuf\Serializer', $serializer);
        $this->assertSame($stream, $serializer->serialize($message));
    }

    public function testUnserializeMessage()
    {
        $class      = FooStub_MessageSerializerTest::CLASS;
        $config     = new Configuration();
        $serializer = new MessageSerializer($config);
        $stream     = Stream::create();

        FooStub_MessageSerializerTest::$calls = [];

        $this->assertInstanceOf($class, $serializer->unserialize($class, $stream));

        $this->assertCount(1, FooStub_MessageSerializerTest::$calls);
        $this->assertSame($stream, FooStub_MessageSerializerTest::$calls[0][0]);
        $this->assertSame($config, FooStub_MessageSerializerTest::$calls[0][1]);
    }

    public function testGetConfiguration()
    {
        $config1 = new Configuration();
        $config2 = Configuration::getInstance();

        $serializer1 = new MessageSerializer($config1);
        $serializer2 = new MessageSerializer();

        $this->assertSame($config1, $serializer1->getConfiguration());
        $this->assertSame($config2, $serializer2->getConfiguration());
    }
}

class FooStub_MessageSerializerTest extends \Protobuf\AbstractMessage
{
    public static $calls = [];

    /**
     * {@inheritdoc}
     */
    public function __construct($stream = null, \Protobuf\Configuration $configuration = null)
    {
        self::$calls[] = func_get_args();
    }

    public static function fromStream($stream, \Protobuf\Configuration $configuration = null)
    {
        throw new \BadMethodCallException(__METHOD__);
    }

    public function extensions()
    {
        throw new \BadMethodCallException(__METHOD__);
    }

    public function unknownFieldSet()
    {
        throw new \BadMethodCallException(__METHOD__);
    }

    public function toStream(\Protobuf\Configuration $configuration = null)
    {
        throw new \BadMethodCallException(__METHOD__);
    }

    public function writeTo(\Protobuf\WriteContext $context)
    {
        throw new \BadMethodCallException(__METHOD__);
    }

    public function readFrom(\Protobuf\ReadContext $context)
    {
        throw new \BadMethodCallException(__METHOD__);
    }

    public function serializedSize(\Protobuf\ComputeSizeContext $context)
    {
        throw new \BadMethodCallException(__METHOD__);
    }

    public function merge(\Protobuf\Message $message)
    {
        throw new \BadMethodCallException(__METHOD__);
    }

    public function clear()
    {
        throw new \BadMethodCallException(__METHOD__);
    }
}
