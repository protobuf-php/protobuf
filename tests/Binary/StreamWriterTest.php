<?php

namespace ProtobufTest\Binary;

use Protobuf\Stream;
use Protobuf\WireFormat;
use ProtobufTest\TestCase;
use Protobuf\Binary\StreamWriter;

class StreamWriterTest extends TestCase
{
    public function testWriteSimpleMessage()
    {
        $stream = Stream::create();
        $writer = new StreamWriter($this->config);
        $binary = $this->getProtoContent('simple.bin');

        $writer->writeVarint($stream, WireFormat::getFieldKey(1, WireFormat::WIRE_FIXED64));
        $writer->writeDouble($stream, 123456789.12345);

        $writer->writeVarint($stream, WireFormat::getFieldKey(2, WireFormat::WIRE_FIXED32));
        $writer->writeFloat($stream, 12345.123046875);

        $writer->writeVarint($stream, WireFormat::getFieldKey(3, WireFormat::WIRE_VARINT));
        $writer->writeVarint($stream, -123456789123456789);

        $writer->writeVarint($stream, WireFormat::getFieldKey(4, WireFormat::WIRE_VARINT));
        $writer->writeVarint($stream, 123456789123456789);

        $writer->writeVarint($stream, WireFormat::getFieldKey(5, WireFormat::WIRE_VARINT));
        $writer->writeVarint($stream, -123456789);

        $writer->writeVarint($stream, WireFormat::getFieldKey(6, WireFormat::WIRE_FIXED64));
        $writer->writeFixed64($stream, 123456789123456789);

        $writer->writeVarint($stream, WireFormat::getFieldKey(7, WireFormat::WIRE_FIXED32));
        $writer->writeFixed32($stream, 123456789);

        $writer->writeVarint($stream, WireFormat::getFieldKey(8, WireFormat::WIRE_VARINT));
        $writer->writeVarint($stream, 1);

        $writer->writeVarint($stream, WireFormat::getFieldKey(9, WireFormat::WIRE_LENGTH));
        $writer->writeString($stream, 'foo');

        $writer->writeVarint($stream, WireFormat::getFieldKey(12, WireFormat::WIRE_LENGTH));
        $writer->writeByteStream($stream, Stream::wrap('bar'));

        $writer->writeVarint($stream, WireFormat::getFieldKey(13, WireFormat::WIRE_VARINT));
        $writer->writeVarint($stream, 123456789);

        $writer->writeVarint($stream, WireFormat::getFieldKey(15, WireFormat::WIRE_FIXED32));
        $writer->writeSFixed32($stream, -123456789);

        $writer->writeVarint($stream, WireFormat::getFieldKey(16, WireFormat::WIRE_FIXED64));
        $writer->writeSFixed64($stream, -123456789123456789);

        $writer->writeVarint($stream, WireFormat::getFieldKey(17, WireFormat::WIRE_VARINT));
        $writer->writeZigzag($stream, -123456789, 32);

        $writer->writeVarint($stream, WireFormat::getFieldKey(18, WireFormat::WIRE_VARINT));
        $writer->writeZigzag($stream, -123456789123456789, 64);

        $this->assertEquals($binary, (string)$stream);
    }

    public function testWriteStream()
    {
        $source = Stream::create();
        $target = Stream::create();
        $writer = new StreamWriter($this->config);

        $writer->writeVarint($source, WireFormat::getFieldKey(1, WireFormat::WIRE_FIXED64));
        $writer->writeDouble($source, 123456789.12345);

        $source->seek(0);
        $writer->writeStream($target, $source);

        $this->assertEquals((string) $source, (string) $target);
    }
}