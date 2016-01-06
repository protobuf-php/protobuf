<?php

namespace ProtobufTest;

use Protobuf\Stream;
use Protobuf\Message;
use ProtobufTest\TestCase;
use ProtobufTest\Protos\Person;
use ProtobufTest\Protos\Simple;
use ProtobufTest\Protos\Complex;

/**
 * @group protoc
 */
class ProtocSerializeMessageTest extends TestCase
{
    public function simpleMessageProvider()
    {
        $max = pow(2, 54) - 1;
        $min = -$max;

        return [
            ['double', 1],
            ['double', 0.1],
            ['double', 1.0],
            ['double', -1],
            ['double', -0.1],
            ['double', -100000],
            ['double', 123456789.12345],
            ['double', -123456789.12345],

            ['float', 1],
            ['float', 0.1],
            ['float', 1.0],
            ['float', -1],
            ['float', -0.1],
            ['float', -100000],
            ['float', 12345.123],
            ['float', -12345.123],

            ['int64', 0],
            ['int64', 1],
            ['int64', -1],
            ['int64', 123456789123456789],
            ['int64', -123456789123456789],
            ['int64', $min],

            ['int64', 0],
            ['int64', 1],
            ['int64', 1000],
            ['int64', 123456789123456789],
            ['int64', PHP_INT_MAX],
            ['int64', $max],

            ['int32', 0],
            ['int32', 1],
            ['int32', -1],
            ['int32', 123456789],
            ['int32', -123456789],

            ['fixed64', 0],
            ['fixed64', 1],
            ['fixed64', 1000],
            ['fixed64', 123456789123456789],

            ['fixed32', 0],
            ['fixed32', 1],
            ['fixed32', 1000],
            ['fixed32', 123456789],

            ['bool', 0],
            ['bool', 1],

            ['string', ''],
            ['string', 'foo'],

            ['bytes', Stream::wrap('')],
            ['bytes', Stream::wrap('foo')],

            ['uint32', 0],
            ['uint32', 1],
            ['uint32', 1000],
            ['uint32', 123456789],

            ['sfixed32', 0],
            ['sfixed32', 1],
            ['sfixed32', -1],
            ['sfixed32', 123456789],
            ['sfixed32', -123456789],

            ['sfixed64', 0],
            ['sfixed64', 1],
            ['sfixed64', -1],
            ['sfixed64', 123456789123456789],
            ['sfixed64', -123456789123456789],

            ['sint32', 0],
            ['sint32', 1],
            ['sint32', -1],
            ['sint32', 123456789],
            ['sint32', -123456789],

            ['sint64', 0],
            ['sint64', 1],
            ['sint64', -1],
            ['sint64', 123456789123456789],
            ['sint64', -123456789123456789],
            ['sint64', $max],
            ['sint64', $min],
        ];
    }

    /**
     * @dataProvider simpleMessageProvider
     */
    public function testEncodeSimpleMessageComparingTypesWithProtoc($field, $value)
    {
        $escaped = $value;
        $proto   = 'simple';
        $message = new Simple();
        $setter  = 'set' . ucfirst($field);
        $class   = 'ProtobufTest.Protos.Simple';

        if (is_string($value)) {
            $escaped  = '"' . $value . '"';
        }

        if ($value instanceof \Protobuf\Stream) {
            $tell    = $value->tell();
            $escaped = '"' . $value . '"';

            $value->seek($tell);
        }

        $message->$setter($value);

        $encoded  = $message->toStream();
        $expected = $this->executeProtoc("$field: $escaped", $class, $proto);

        $this->assertEquals(bin2hex($expected), bin2hex($encoded), "Encoding $field with value $value");
    }

    /**
     * @dataProvider simpleMessageProvider
     */
    public function testDecodeSimpleMessageComparingTypesWithProtoc($field, $value)
    {
        $escaped = $value;
        $proto   = 'simple';
        $getter  = 'get' . ucfirst($field);
        $class   = 'ProtobufTest.Protos.Simple';

        if (is_string($value)) {
            $escaped  = '"' . $value . '"';
        }

        if ($value instanceof \Protobuf\Stream) {
            $tell    = $value->tell();
            $escaped = '"' . $value . '"';

            $value->seek($tell);
        }

        $binary   = $this->executeProtoc("$field: $escaped", $class, $proto);
        $message  = Simple::fromStream(Stream::wrap($binary));
        $result   = $message->$getter();

        // Hack the comparison for float precision
        if (is_float($value)) {
            $precision = strlen($value) - strpos($value, '.');
            $result    = round($result, $precision);
        }

        if ($result instanceof \Protobuf\Stream) {
            $result = (string) $result;
        }

        $this->assertEquals($value, $result, "Decoding $field with value $value");
    }

    public function testEncodeAndDecodeEnumComparingWithProtoc()
    {
        $proto   = 'complex';
        $complex = new Complex();
        $value   = Complex\Enum::FOO();
        $class   = 'ProtobufTest.Protos.Complex';

        $complex->setEnum($value);

        $encoded  = $complex->toStream();
        $expected = $this->executeProtoc("enum: FOO", $class, $proto);
        $decoded  = Complex::fromStream(Stream::wrap($expected));

        $this->assertInstanceOf(Complex::CLASS, $decoded);
        $this->assertEquals(bin2hex($expected), bin2hex($encoded));
        $this->assertEquals(Complex\Enum::FOO(), $decoded->getEnum());
    }

    public function testEncodeAndDecodeNestedMessageComparingWithProtoc()
    {
        $proto   = 'complex';
        $complex = new Complex();
        $nested  = new Complex\Nested();
        $input   = 'nested { foo: "FOO" }';
        $class   = 'ProtobufTest.Protos.Complex';

        $nested->setFoo('FOO');
        $complex->setNested($nested);

        $encoded  = $complex->toStream();
        $expected = $this->executeProtoc($input, $class, $proto);
        $decoded  = Complex::fromStream(Stream::wrap($expected));

        $this->assertInstanceOf(Complex::CLASS, $decoded);
        $this->assertInstanceOf(Complex\Nested::CLASS, $complex->getNested());
        $this->assertEquals(bin2hex($encoded), bin2hex($expected));
        $this->assertEquals($complex->getNested()->getFoo(), 'FOO');
    }

    protected function executeProtoc($input, $class, $proto)
    {
        $path     = __DIR__ . '/Resources';
        $command  = "echo '$input' | protoc --encode=$class -I$path $path/$proto.proto";
        $output   = null;
        $exitCode = null;

        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            $this->fail("Fail to run protoc : [$command]");
        }

        return implode(PHP_EOL, $output);
    }
}
