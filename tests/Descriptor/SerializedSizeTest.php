<?php

namespace ProtobufTest\Descriptor;

use Protobuf\Binary\SizeCalculator;
use Protobuf\ComputeSizeContext;

use ProtobufTest\TestCase;
use ProtobufTest\Protos\Simple;
use ProtobufTest\Protos\Person;
use ProtobufTest\Protos\Repeated;
use ProtobufTest\Protos\AddressBook;
use Protobuf\Extension\ExtensionFieldMap;

class SerializedSizeTest extends TestCase
{
    public function testSimpleSerializedSize()
    {
        $simple     = new Simple();
        $calculator = $this->getMock(SizeCalculator::CLASS, [], [], '', false);
        $context    = $this->getMock(ComputeSizeContext::CLASS, [], [], '', false);

        $simple->setBool(true);
        $simple->setBytes("bar");
        $simple->setString("foo");
        $simple->setFloat(12345.123);
        $simple->setUint32(123456789);
        $simple->setInt32(-123456789);
        $simple->setFixed32(123456789);
        $simple->setSint32(-123456789);
        $simple->setSfixed32(-123456789);
        $simple->setDouble(123456789.12345);
        $simple->setInt64(-123456789123456789);
        $simple->setUint64(123456789123456789);
        $simple->setFixed64(123456789123456789);
        $simple->setSint64(-123456789123456789);
        $simple->setSfixed64(-123456789123456789);

        $context->expects($this->once())
            ->method('getSizeCalculator')
            ->willReturn($calculator);

        $calculator->expects($this->exactly(4))
            ->method('computeVarintSize')
            ->will($this->returnValueMap([
                [-123456789123456789, 10],
                [123456789123456789, 9],
                [-123456789, 10],
                [123456789, 4],
            ]));

        $calculator->expects($this->exactly(2))
            ->method('computeStringSize')
            ->will($this->returnValueMap([
                ["foo", 3],
                ["bar", 3]
            ]));

        $calculator->expects($this->once())
            ->method('computeZigzag32Size')
            ->with(-123456789)
            ->willReturn(4);

        $calculator->expects($this->once())
            ->method('computeZigzag64Size')
            ->with(-123456789123456789)
            ->willReturn(9);

        $simple->serializedSize($context);
    }

    public function testRepeatedStringSerializedSize()
    {
        $repeated   = new Repeated();
        $calculator = $this->getMock(SizeCalculator::CLASS, [], [], '', false);
        $context    = $this->getMock(ComputeSizeContext::CLASS, [], [], '', false);

        $repeated->addString('one');
        $repeated->addString('two');
        $repeated->addString('three');

        $context->expects($this->once())
            ->method('getSizeCalculator')
            ->willReturn($calculator);

        $calculator->expects($this->exactly(3))
            ->method('computeStringSize')
            ->will($this->returnValueMap([
                ['one', 4],
                ['two', 4],
                ['three', 6]
            ]));

        $repeated->serializedSize($context);
    }

    public function testRepeatedInt32SerializedSize()
    {
        $repeated   = new Repeated();
        $calculator = $this->getMock(SizeCalculator::CLASS, [], [], '', false);
        $context    = $this->getMock(ComputeSizeContext::CLASS, [], [], '', false);

        $repeated->addInt(1);
        $repeated->addInt(2);
        $repeated->addInt(2);

        $context->expects($this->once())
            ->method('getSizeCalculator')
            ->willReturn($calculator);

        $calculator->expects($this->exactly(3))
            ->method('computeVarintSize')
            ->will($this->returnValueMap([
                [1, 1],
                [2, 1],
                [3, 1]
            ]));

        $repeated->serializedSize($context);
    }

    public function testAddressBookWithExtensionsSerializedSize()
    {
        $message    = new AddressBook();
        $person     = $this->getMock(Person::CLASS);
        $calculator = $this->getMock(SizeCalculator::CLASS, [], [], '', false);
        $extensions = $this->getMock(ExtensionFieldMap::CLASS, [], [], '', false);
        $context    = $this->getMock(ComputeSizeContext::CLASS, [], [], '', false);
        $personSize = 2;
        $extSize    = 4;

        $message->addPerson($person);
        $this->setPropertyValue($message, 'extensions', $extensions);

        $context->expects($this->once())
            ->method('getSizeCalculator')
            ->willReturn($calculator);

        $calculator->expects($this->once())
            ->method('computeVarintSize')
            ->with($this->equalTo($personSize))
            ->willReturn(1);

        $extensions->expects($this->once())
            ->method('serializedSize')
            ->willReturn($extSize)
            ->with($context);

        $person->expects($this->once())
            ->method('serializedSize')
            ->willReturn($personSize)
            ->with($context);

        $message->serializedSize($context);
    }
}
