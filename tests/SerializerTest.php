<?php

namespace ProtobufTest;

use ProtobufTest\Protos\Simple;

use Protobuf\Configuration;
use Protobuf\Serializer;
use Protobuf\Message;
use Protobuf\Stream;


class SerializerTest extends TestCase
{
    public function testSerializeMessage()
    {
        $message    = $this->getMock(Message::CLASS);
        $config     = new Configuration();
        $serializer = new Serializer($config);
        $stream     = Stream::create();

        $message->expects($this->once())
            ->method('toStream')
            ->willReturn($stream)
            ->with($this->equalTo($config));

        $this->assertSame($stream, $serializer->serialize($message));
    }

    public function testUnserializeMessage()
    {
        $message    = $this->getMock(Message::CLASS);
        $class      = MessageSerializerTest::CLASS;
        $config     = new Configuration();
        $serializer = new Serializer($config);
        $stream     = Stream::create();

        MessageSerializerTest::$calls   = [];
        MessageSerializerTest::$returns = [$message];

        $this->assertSame($message, $serializer->unserialize($class, $stream));

        $this->assertCount(1, MessageSerializerTest::$calls);
        $this->assertSame($stream, MessageSerializerTest::$calls[0][0]);
        $this->assertSame($config, MessageSerializerTest::$calls[0][1]);
    }
}

class MessageSerializerTest extends \Protobuf\AbstractMessage
{
    public static $calls = [];
    public static $returns = [];

    public static function fromStream($stream, \Protobuf\Configuration $configuration = null)
    {
        self::$calls[] = func_get_args();

        return array_pop(self::$returns);
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
}
