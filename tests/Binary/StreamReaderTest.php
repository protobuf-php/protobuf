<?php

namespace ProtobufTest\Binary;

use Protobuf\Stream;
use Protobuf\WireFormat;
use ProtobufTest\TestCase;
use Protobuf\Binary\StreamReader;

class StreamReaderTest extends TestCase
{
    protected function assertNextTagWire(StreamReader $reader, Stream $stream, $expectedTag, $expectedWire)
    {
        $key  = $reader->readVarint($stream);
        $tag  = WireFormat::getTagFieldNumber($key);
        $wire = WireFormat::getTagWireType($key);

        $this->assertEquals($expectedTag, $tag);
        $this->assertEquals($expectedWire, $wire);
    }

    public function testReadSimpleMessage()
    {
        $stream = Stream::wrap($this->getProtoContent('simple.bin'));
        $reader = new StreamReader($this->config);

        $this->assertNextTagWire($reader, $stream, 1, WireFormat::WIRE_FIXED64);
        $this->assertEquals(123456789.12345, $reader->readDouble($stream));

        $this->assertNextTagWire($reader, $stream, 2, WireFormat::WIRE_FIXED32);
        $this->assertEquals(12345.123046875, $reader->readFloat($stream));

        $this->assertNextTagWire($reader, $stream, 3, WireFormat::WIRE_VARINT);
        $this->assertEquals(-123456789123456789, $reader->readVarint($stream));

        $this->assertNextTagWire($reader, $stream, 4, WireFormat::WIRE_VARINT);
        $this->assertEquals(123456789123456789, $reader->readVarint($stream));

        $this->assertNextTagWire($reader, $stream, 5, WireFormat::WIRE_VARINT);
        $this->assertEquals(-123456789, $reader->readVarint($stream));

        $this->assertNextTagWire($reader, $stream, 6, WireFormat::WIRE_FIXED64);
        $this->assertEquals(123456789123456789, $reader->readFixed64($stream));

        $this->assertNextTagWire($reader, $stream, 7, WireFormat::WIRE_FIXED32);
        $this->assertEquals(123456789, $reader->readFixed32($stream));

        $this->assertNextTagWire($reader, $stream, 8, WireFormat::WIRE_VARINT);
        $this->assertEquals(true, $reader->readBool($stream));

        $this->assertNextTagWire($reader, $stream, 9, WireFormat::WIRE_LENGTH);
        $this->assertEquals('foo', $reader->readString($stream));

        $this->assertNextTagWire($reader, $stream, 12, WireFormat::WIRE_LENGTH);
        $this->assertInstanceOf('Protobuf\Stream', ($byteStream = $reader->readByteStream($stream)));
        $this->assertEquals('bar', (string) $byteStream);

        $this->assertNextTagWire($reader, $stream, 13, WireFormat::WIRE_VARINT);
        $this->assertEquals(123456789, $reader->readVarint($stream));

        $this->assertNextTagWire($reader, $stream, 15, WireFormat::WIRE_FIXED32);
        $this->assertEquals(-123456789, $reader->readSFixed32($stream));

        $this->assertNextTagWire($reader, $stream, 16, WireFormat::WIRE_FIXED64);
        $this->assertEquals(-123456789123456789, $reader->readSFixed64($stream));

        $this->assertNextTagWire($reader, $stream, 17, WireFormat::WIRE_VARINT);
        $this->assertEquals(-123456789, $reader->readZigzag($stream));

        $this->assertNextTagWire($reader, $stream, 18, WireFormat::WIRE_VARINT);
        $this->assertEquals(-123456789123456789, $reader->readZigzag($stream));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Groups are deprecated in Protocol Buffers and unsupported.
     */
    public function testReadUnknownWireFormatGroupException()
    {
        $stream = Stream::create($this->getProtoContent('simple.bin'));
        $reader = new StreamReader($this->config);

        $reader->readUnknown($stream, WireFormat::WIRE_GROUP_START);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unsupported wire type '-1' while reading unknown field.
     */
    public function testReadUnknownWireFormatException()
    {
        $stream = Stream::create($this->getProtoContent('simple.bin'));
        $reader = new StreamReader($this->config);

        $reader->readUnknown($stream, -1);
    }
}