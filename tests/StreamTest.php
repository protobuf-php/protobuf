<?php

namespace ProtobufTest;

use Protobuf\Stream;

class StreamTest extends TestCase
{
    public function testClosesStreamOnDestruct()
    {
        $handle = fopen('php://temp', 'r');
        $stream = new Stream($handle);

        unset($stream);

        $this->assertFalse(is_resource($handle));
    }

    public function testConvertsToString()
    {
        $handle = fopen('php://temp', 'w+');
        $stream = new Stream($handle);

        fwrite($handle, 'data');

        $this->assertEquals('data', (string) $stream);
        $this->assertEquals('data', (string) $stream);
    }

    public function testGetsContents()
    {
        $handle = fopen('php://temp', 'w+');
        $stream = new Stream($handle);

        fwrite($handle, 'data');

        $this->assertEquals('data', $stream->getContents());
    }

    public function testChecksEof()
    {
        $handle = fopen('php://temp', 'w+');
        $stream = new Stream($handle);

        fwrite($handle, 'data');

        $this->assertFalse($stream->eof());
        $stream->read(4);
        $this->assertTrue($stream->eof());
    }

    public function testGetSize()
    {
        $handle = fopen('php://temp', 'w+');
        $stream = new Stream($handle);

        $this->assertEquals(3, fwrite($handle, 'foo'));
        $this->assertEquals(3, $stream->getSize());
        $this->assertEquals(4, $stream->write('test', strlen('test')));
        $this->assertEquals(7, $stream->getSize());
        $this->assertEquals(7, $stream->getSize());
    }

    public function testStreamPosition()
    {
        $handle = fopen('php://temp', 'w+');
        $stream = new Stream($handle);

        $this->assertEquals(0, $stream->tell());
        $stream->write('foo', strlen('foo'));
        $this->assertEquals(3, $stream->tell());

        $stream->seek(1);

        $this->assertEquals(1, $stream->tell());
        $this->assertSame(ftell($handle), $stream->tell());
    }

    public function testWriteStream()
    {
        $source = Stream::create();
        $target = Stream::create();

        $source->write('foo', strlen('foo'));
        $source->seek(0);

        $target->writeStream($source, $source->getSize());

        $this->assertEquals(3, $source->getSize());
        $this->assertEquals(3, $target->getSize());

        $this->assertEquals('foo', (string) $source);
        $this->assertEquals('foo', (string) $target);
    }

    public function testReadStream()
    {
        $source = Stream::wrap('FOObar');
        $read1  = $source->readStream(3);
        $read2  = $source->readStream(3);

        $this->assertInstanceOf('Protobuf\Stream', $read1);
        $this->assertInstanceOf('Protobuf\Stream', $read2);

        $this->assertEquals(3, $read1->getSize());
        $this->assertEquals(3, $read2->getSize());

        $this->assertEquals('FOO', (string) $read1);
        $this->assertEquals('bar', (string) $read2);
    }

    public function testPositionOfResource()
    {
        $handle = fopen(__FILE__, 'r');

        fseek($handle, 10);

        $stream = Stream::wrap($handle);

        $this->assertEquals(10, $stream->tell());
    }

    public function testCreateStreamFromString()
    {
        $stream = Stream::wrap('foo');

        $this->assertInstanceOf('Protobuf\Stream', $stream);
        $this->assertEquals('foo', $stream->getContents());
    }

    public function testCreateStreamFromEmptyString()
    {
        $this->assertInstanceOf('Protobuf\Stream', Stream::wrap());
    }

    public function testCreateStreamFromResource()
    {
        $handle  = fopen(__FILE__, 'r');
        $stream  = Stream::wrap($handle);
        $content = file_get_contents(__FILE__);

        $this->assertInstanceOf('Protobuf\Stream', $stream);
        $this->assertSame($content, (string) $stream);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Failed to write 2 bytes
     */
    public function testWriteException()
    {
        $handle = fopen('php://temp', 'w+');
        $stream = new Stream($handle);

        $stream->write('', 2);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to seek stream position to -1
     */
    public function testSeekException()
    {
        $handle = fopen('php://temp', 'w+');
        $stream = new Stream($handle);

        $stream->seek(-1);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Failed to write stream with 3 bytes
     */
    public function testWriteStreamException()
    {
        $source = Stream::create();
        $target = Stream::create();

        $target->writeStream($source, 3);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionForUnknown()
    {
        Stream::wrap(new \stdClass());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnInvalidArgument()
    {
        new Stream(null);
    }

    public function testCanSetSize()
    {
        $this->assertEquals(10, Stream::wrap('', 10)->getSize());
    }
}
