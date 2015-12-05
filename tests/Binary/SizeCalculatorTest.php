<?php

namespace ProtobufTest\Binary;

use Protobuf\Stream;
use ProtobufTest\TestCase;
use Protobuf\Binary\StreamWriter;
use Protobuf\Binary\SizeCalculator;

class SizeCalculatorTest extends TestCase
{
    /**
     * @var \Protobuf\Binary\SizeCalculator
     */
    protected $calculator;

    /**
     * @var \Protobuf\Binary\StreamWriter
     */
    protected $writer;

    protected function setUp()
    {
        parent::setUp();

        $this->writer     = new StreamWriter($this->config);
        $this->calculator = new SizeCalculator($this->config);
    }

    public function varintProvider()
    {
        return [
            [1],
            [-1],
            [123456789],
            [-123456789],
            [123456789123456789],
            [-123456789123456789]
        ];
    }

    /**
     * @dataProvider varintProvider
     */
    public function testComputeVarintSize($value)
    {
        $stream = Stream::create();

        $this->writer->writeVarint($stream, $value);

        $streamSize = $stream->getSize();
        $actualSize = $this->calculator->computeVarintSize($value);

        $this->assertEquals($streamSize, $actualSize);
    }

    public function providerZigZag32()
    {
        return [
            [1],
            [-1],
            [123456789],
            [-123456789]
        ];
    }

    /**
     * @dataProvider providerZigZag32
     */
    public function testComputeZigZag32Size($value)
    {
        $stream = Stream::create();

        $this->writer->writeZigZag32($stream, $value);

        $streamSize = $stream->getSize();
        $actualSize = $this->calculator->computeZigzag32Size($value);

        $this->assertEquals($streamSize, $actualSize);
    }

    public function providerZigZag64()
    {
        return [
            [1],
            [-1],
            [123456789],
            [-123456789]
        ];
    }

    /**
     * @dataProvider providerZigZag64
     */
    public function testComputeZigZag64Size($value)
    {
        $stream = Stream::create();

        $this->writer->writeZigZag64($stream, $value);

        $streamSize = $stream->getSize();
        $actualSize = $this->calculator->computeZigzag64Size($value);

        $this->assertEquals($streamSize, $actualSize);
    }

    public function providerSFixed32()
    {
        return [
            [1],
            [-1],
            [123456789],
            [-123456789]
        ];
    }

    /**
     * @dataProvider providerSFixed32
     */
    public function testComputeSFixed32Size($value)
    {
        $stream = Stream::create();

        $this->writer->writeSFixed32($stream, $value);

        $streamSize = $stream->getSize();
        $actualSize = $this->calculator->computeSFixed32Size($value);

        $this->assertEquals($streamSize, $actualSize);
    }

    public function providerFixed32()
    {
        return [
            [1],
            [1000],
            [123456789]
        ];
    }

    /**
     * @dataProvider providerFixed32
     */
    public function testComputeFixed32Size($value)
    {
        $stream = Stream::create();

        $this->writer->writeFixed32($stream, $value);

        $streamSize = $stream->getSize();
        $actualSize = $this->calculator->computeFixed32Size($value);

        $this->assertEquals($streamSize, $actualSize);
    }

    public function providerSFixed64()
    {
        return [
            [1],
            [-1],
            [123456789],
            [-123456789],
            [123456789123456789],
            [-123456789123456789]
        ];
    }

    /**
     * @dataProvider providerSFixed64
     */
    public function testComputeSFixed64Size($value)
    {
        $stream = Stream::create();

        $this->writer->writeSFixed64($stream, $value);

        $streamSize = $stream->getSize();
        $actualSize = $this->calculator->computeSFixed64Size($value);

        $this->assertEquals($streamSize, $actualSize);
    }

    public function providerFixed64()
    {
        return [
            [1],
            [123456789],
            [123456789123456789]
        ];
    }

    /**
     * @dataProvider providerFixed64
     */
    public function testComputeFixed64Size($value)
    {
        $stream = Stream::create();

        $this->writer->writeFixed64($stream, $value);

        $streamSize = $stream->getSize();
        $actualSize = $this->calculator->computeFixed64Size($value);

        $this->assertEquals($streamSize, $actualSize);
    }

    public function providerFloat()
    {
        return [
            [1.1],
            [-1.1],
            [123456789.2],
            [-123456789.2],
            [12345.123046875],
            [-12345.123046875]
        ];
    }

    /**
     * @dataProvider providerFloat
     */
    public function testComputeFloatSize($value)
    {
        $stream = Stream::create();

        $this->writer->writeFloat($stream, $value);

        $streamSize = $stream->getSize();
        $actualSize = $this->calculator->computeFloatSize($value);

        $this->assertEquals($streamSize, $actualSize);
    }

    public function providerDouble()
    {
        return [
            [1.1],
            [-1.1],
            [12345.12345],
            [-12345.12345],
            [123456789.12345],
            [-123456789.12345]
        ];
    }

    /**
     * @dataProvider providerDouble
     */
    public function testComputeDoubleSize($value)
    {
        $stream = Stream::create();

        $this->writer->writeDouble($stream, $value);

        $streamSize = $stream->getSize();
        $actualSize = $this->calculator->computeDoubleSize($value);

        $this->assertEquals($streamSize, $actualSize);
    }

    public function providerBool()
    {
        return [
            [1],
            [0],
            [true],
            [false]
        ];
    }

    /**
     * @dataProvider providerBool
     */
    public function testComputeBoolSize($value)
    {
        $stream = Stream::create();

        $this->writer->writeBool($stream, $value);

        $streamSize = $stream->getSize();
        $actualSize = $this->calculator->computeBoolSize($value);

        $this->assertEquals($streamSize, $actualSize);
    }

    public function providerString()
    {
        return [
            ['foo'],
            ['http://www.lipsum.com/'],
            ['Neque porro quisquam est qui dolorem ipsum quia dolor sit amet']
        ];
    }

    /**
     * @dataProvider providerString
     */
    public function testComputeStringSize($value)
    {
        $stream = Stream::create();

        $this->writer->writeString($stream, $value);

        $streamSize = $stream->getSize();
        $actualSize = $this->calculator->computeStringSize($value);

        $this->assertEquals($streamSize, $actualSize);
    }

    public function providerByteStream()
    {
        return [
            [Stream::create('foo')],
            [Stream::create('http://www.lipsum.com/')],
            [Stream::create('Neque porro quisquam est qui dolorem ipsum quia dolor sit amet')]
        ];
    }

    /**
     * @dataProvider providerByteStream
     */
    public function testComputeByteStreamSize($value)
    {
        $stream = Stream::create();

        $this->writer->writeByteStream($stream, $value);

        $streamSize = $stream->getSize();
        $actualSize = $this->calculator->computeByteStreamSize($value);

        $this->assertEquals($streamSize, $actualSize);
    }
}