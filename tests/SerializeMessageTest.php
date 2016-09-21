<?php

namespace ProtobufTest;

use Protobuf\Stream;
use Protobuf\Message;
use Protobuf\Collection;
use ProtobufTest\TestCase;
use ProtobufTest\Protos\Tree;
use ProtobufTest\Protos\Person;
use ProtobufTest\Protos\Simple;
use ProtobufTest\Protos\Repeated;
use ProtobufTest\Protos\Extension;
use ProtobufTest\Protos\AddressBook;
use ProtobufTest\Protos\Unrecognized;
use ProtobufTest\Protos\Person\PhoneType;
use ProtobufTest\Protos\Person\PhoneNumber;
use ProtobufTest\Protos\Options\ParentMessage;

class SerializeMessageTest extends TestCase
{

    private function assertSerializedMessageSize($expectedContent, $message)
    {
        $context      = $this->config->createComputeSizeContext();
        $expectedSize = mb_strlen($expectedContent, '8bit');
        $actualSize   = $message->serializedSize($context);

        $this->assertEquals($expectedSize, $actualSize);
    }

    public function testWriteSimpleMessage()
    {
        $simple = new Simple();

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

        $expected = $this->getProtoContent('simple.bin');
        $actual   = $simple->toStream();

        $this->assertEquals($expected, (string) $actual);
        $this->assertSerializedMessageSize($expected, $simple);
    }

    public function testWriteSimpleMessageTwice()
    {
        $simple = new Simple();

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

        $expected = $this->getProtoContent('simple.bin');
        $actual1  = $simple->toStream();
        $actual2  = $simple->toStream();

        $this->assertEquals($expected, (string) $actual1);
        $this->assertEquals($expected, (string) $actual2);
    }

    public function testWriteRepeatedString()
    {
        $repeated = new Repeated();

        $repeated->addString('one');
        $repeated->addString('two');
        $repeated->addString('three');

        $expected = $this->getProtoContent('repeated-string.bin');
        $actual   = $repeated->toStream();

        $this->assertEquals($expected, (string) $actual);
        $this->assertSerializedMessageSize($expected, $repeated);
    }

    public function testWriteRepeatedInt32()
    {
        $repeated = new Repeated();

        $repeated->addInt(1);
        $repeated->addInt(2);
        $repeated->addInt(3);

        $expected = $this->getProtoContent('repeated-int32.bin');
        $actual   = $repeated->toStream();

        $this->assertEquals($expected, (string) $actual);
        $this->assertSerializedMessageSize($expected, $repeated);
    }

    public function testWriteRepeatedNested()
    {
        $repeated = new Repeated();
        $nested1  = new Repeated\Nested();
        $nested2  = new Repeated\Nested();
        $nested3  = new Repeated\Nested();

        $nested1->setId(1);
        $nested2->setId(2);
        $nested3->setId(3);

        $repeated->addNested($nested1);
        $repeated->addNested($nested2);
        $repeated->addNested($nested3);

        $expected = $this->getProtoContent('repeated-nested.bin');
        $actual   = $repeated->toStream();

        $this->assertEquals($expected, (string) $actual);
        $this->assertSerializedMessageSize($expected, $repeated);
    }

    public function testWriteRepeatedPacked()
    {
        $repeated = new Repeated();

        $repeated->addPacked(1);
        $repeated->addPacked(2);
        $repeated->addPacked(3);

        $expected = $this->getProtoContent('repeated-packed.bin');
        $actual   = $repeated->toStream();

        $this->assertEquals($expected, (string) $actual);
        $this->assertSerializedMessageSize($expected, $repeated);
    }

    public function testWriteRepeatedBytes()
    {
        $repeated = new Repeated();

        $repeated->addBytes('bin1');
        $repeated->addBytes('bin2');
        $repeated->addBytes('bin3');

        $expected = $this->getProtoContent('repeated-bytes.bin');
        $actual   = $repeated->toStream();

        $this->assertEquals($expected, (string) $actual);
        $this->assertSerializedMessageSize($expected, $repeated);
    }

    public function testWriteRepeatedEnum()
    {
        $repeated = new Repeated();

        $repeated->addEnum(Repeated\Enum::FOO());
        $repeated->addEnum(Repeated\Enum::BAR());

        $expected = $this->getProtoContent('repeated-enum.bin');
        $actual   = $repeated->toStream();

        $this->assertEquals($expected, (string) $actual);
        $this->assertSerializedMessageSize($expected, $repeated);
    }

    public function testWriteRepeatedPackedEnum()
    {
        $repeated = new Repeated();

        $repeated->addPackedEnum(Repeated\Enum::FOO());
        $repeated->addPackedEnum(Repeated\Enum::BAR());

        $expected = $this->getProtoContent('repeated-packed-enum.bin');
        $actual   = $repeated->toStream();

        $this->assertEquals($expected, (string) $actual);
        $this->assertSerializedMessageSize($expected, $repeated);
    }

    public function testWriteComplexMessage()
    {
        $phone1  = new PhoneNumber();
        $phone2  = new PhoneNumber();
        $phone3  = new PhoneNumber();
        $book    = new AddressBook();
        $person1 = new Person();
        $person2 = new Person();

        $person1->setId(2051);
        $person1->setName('John Doe');
        $person1->setEmail('john.doe@gmail.com');

        $person2->setId(23);
        $person2->setName('Iván Montes');
        $person2->setEmail('drslump@pollinimini.net');

        $book->addPerson($person1);
        $book->addPerson($person2);

        $person1->addPhone($phone1);
        $person1->addPhone($phone2);

        $phone1->setNumber('1231231212');
        $phone1->setType(PhoneType::HOME());

        $phone2->setNumber('55512321312');
        $phone2->setType(PhoneType::MOBILE());

        $phone3->setNumber('3493123123');
        $phone3->setType(PhoneType::WORK());

        $person2->addPhone($phone3);

        $expected = $this->getProtoContent('addressbook.bin');
        $actual   = $book->toStream();

        $this->assertEquals($expected, (string) $actual);
        $this->assertSerializedMessageSize($expected, $book);
    }

    public function testWritePhpOptionsMessage()
    {
        $parentMessage = new ParentMessage();
        $innerMessage1 = new ParentMessage\InnerMessage();
        $innerMessage2 = new ParentMessage\InnerMessage();

        $innerMessage1->setEnum(ParentMessage\InnerMessage\InnerMessageEnum::VALUE1());
        $innerMessage2->setEnum(ParentMessage\InnerMessage\InnerMessageEnum::VALUE2());

        $parentMessage->addInner($innerMessage1);
        $parentMessage->addInner($innerMessage2);
        $parentMessage->setEnum(ParentMessage\InnerEnum::VALUE1());

        $expected = $this->getProtoContent('php_options.bin');
        $actual   = $parentMessage->toStream();

        $this->assertEquals($expected, (string) $actual);
        $this->assertSerializedMessageSize($expected, $parentMessage);
    }

    public function testWriteTreeMessage()
    {
        $root  = new Tree\Node();
        $admin = new Tree\Node();
        $fabio = new Tree\Node();

        $root->setPath('/Users');
        $fabio->setPath('/Users/fabio');
        $admin->setPath('/Users/admin');

        // avoid recursion
        $parent = clone $root;

        $admin->setParent($parent);
        $fabio->setParent($parent);

        $root->addChildren($fabio);
        $root->addChildren($admin);

        $expected = $this->getProtoContent('tree.bin');
        $actual   = $root->toStream();

        $this->assertEquals($expected, (string) $actual);
        $this->assertSerializedMessageSize($expected, $root);
    }

    public function testWriteAnimalExtensionMessage()
    {
        $cat    = new Extension\Cat();
        $animal = new Extension\Animal();

        $cat->setDeclawed(true);

        $animal->setType(Extension\Animal\Type::CAT());
        $animal->extensions()->put(Extension\Cat::animal(), $cat);

        $expected = $this->getProtoContent('extension-animal-cat.bin');
        $actual   = $animal->toStream();

        $this->assertEquals($expected, (string) $actual);
        $this->assertSerializedMessageSize($expected, $animal);
    }

    public function testWriteCommandExtensionMessage()
    {
        $version = new Extension\VersionCommand();
        $command = new Extension\Command();

        $version->setVersion(1);
        $version->setProtocol(Extension\VersionCommand\Protocol::V1());

        $command->setType(Extension\Command\CommandType::VERSION());
        $command->extensions()->put(Extension\Extension::verbose(), true);
        $command->extensions()->put(Extension\VersionCommand::cmd(), $version);

        $expected = $this->getProtoContent('extension-command-version.bin');
        $actual   = $command->toStream();

        $this->assertEquals($expected, (string) $actual);
        $this->assertSerializedMessageSize($expected, $command);
    }

    public function testReadSimpleMessage()
    {
        $binary = $this->getProtoContent('simple.bin');
        $simple = Simple::fromStream($binary);

        $this->assertInstanceOf(Simple::CLASS, $simple);
        $this->assertInstanceOf(Stream::CLASS, $simple->getBytes());

        $this->assertInternalType('bool', $simple->getBool());
        $this->assertInternalType('string', $simple->getString());
        $this->assertInternalType('float', $simple->getFloat(), '', 0.0001);
        $this->assertInternalType('integer', $simple->getUint32());
        $this->assertInternalType('integer', $simple->getInt32());
        $this->assertInternalType('integer', $simple->getFixed32());
        $this->assertInternalType('integer', $simple->getSint32());
        $this->assertInternalType('integer', $simple->getSfixed32());
        $this->assertInternalType('float', $simple->getDouble());
        $this->assertInternalType('integer', $simple->getInt64());
        $this->assertInternalType('integer', $simple->getUint64());
        $this->assertInternalType('integer', $simple->getFixed64());
        $this->assertInternalType('integer', $simple->getSint64());
        $this->assertInternalType('integer', $simple->getSfixed64());

        $this->assertEquals(true, $simple->getBool());
        $this->assertEquals("bar", $simple->getBytes());
        $this->assertEquals("foo", $simple->getString());
        $this->assertEquals(12345.123, $simple->getFloat(), '', 0.0001);
        $this->assertEquals(123456789, $simple->getUint32());
        $this->assertEquals(-123456789, $simple->getInt32());
        $this->assertEquals(123456789, $simple->getFixed32());
        $this->assertEquals(-123456789, $simple->getSint32());
        $this->assertEquals(-123456789, $simple->getSfixed32());
        $this->assertEquals(123456789.12345, $simple->getDouble());
        $this->assertEquals(-123456789123456789, $simple->getInt64());
        $this->assertEquals(123456789123456789, $simple->getUint64());
        $this->assertEquals(123456789123456789, $simple->getFixed64());
        $this->assertEquals(-123456789123456789, $simple->getSint64());
        $this->assertEquals(-123456789123456789, $simple->getSfixed64());
    }

    public function testReadRepeatedString()
    {
        $binary   = $this->getProtoContent('repeated-string.bin');
        $repeated = Repeated::fromStream($binary);

        $this->assertInstanceOf(Repeated::CLASS, $repeated);
        $this->assertInstanceOf(Collection::CLASS, $repeated->getStringList());
        $this->assertEquals(['one', 'two', 'three'], $repeated->getStringList()->getArrayCopy());
    }

    public function testReadRepeatedInt32()
    {
        $binary   = $this->getProtoContent('repeated-int32.bin');
        $repeated = Repeated::fromStream($binary);

        $this->assertInstanceOf(Repeated::CLASS, $repeated);
        $this->assertInstanceOf(Collection::CLASS, $repeated->getIntList());
        $this->assertEquals([1, 2, 3], $repeated->getIntList()->getArrayCopy());
    }

    public function testReadRepeatedNested()
    {
        $binary   = $this->getProtoContent('repeated-nested.bin');
        $repeated = Repeated::fromStream($binary);

        $this->assertInstanceOf(Repeated::CLASS, $repeated);
        $this->assertInstanceOf(Collection::CLASS, $repeated->getNestedList());
        $this->assertCount(3, $repeated->getNestedList());

        $this->assertInstanceOf(Repeated\Nested::CLASS, $repeated->getNestedList()[0]);
        $this->assertInstanceOf(Repeated\Nested::CLASS, $repeated->getNestedList()[1]);
        $this->assertInstanceOf(Repeated\Nested::CLASS, $repeated->getNestedList()[2]);

        $this->assertEquals(1, $repeated->getNestedList()[0]->getId());
        $this->assertEquals(2, $repeated->getNestedList()[1]->getId());
        $this->assertEquals(3, $repeated->getNestedList()[2]->getId());
    }

    public function testReadRepeatedPacked()
    {
        $binary   = $this->getProtoContent('repeated-packed.bin');
        $repeated = Repeated::fromStream($binary);

        $this->assertInstanceOf(Repeated::CLASS, $repeated);
        $this->assertInstanceOf(Collection::CLASS, $repeated->getPackedList());
        $this->assertEquals([1, 2, 3], $repeated->getPackedList()->getArrayCopy());
    }

    public function testReadRepeatedPackedEnum()
    {
        $enumVal  = [Repeated\Enum::FOO(), Repeated\Enum::BAR()];
        $binary   = $this->getProtoContent('repeated-packed-enum.bin');
        $repeated = Repeated::fromStream($binary);

        $this->assertInstanceOf(Repeated::CLASS, $repeated);
        $this->assertInstanceOf(Collection::CLASS, $repeated->getPackedEnumList());
        $this->assertEquals($enumVal, $repeated->getPackedEnumList()->getArrayCopy());
    }

    public function testReadRepeatedBytes()
    {
        $binary   = $this->getProtoContent('repeated-bytes.bin');
        $repeated = Repeated::fromStream($binary);

        $this->assertInstanceOf(Repeated::CLASS, $repeated);
        $this->assertInstanceOf(Collection::CLASS, $repeated->getBytesList());
        $this->assertCount(3, $repeated->getBytesList());

        $this->assertInstanceOf('Protobuf\Stream', $repeated->getBytesList()[0]);
        $this->assertInstanceOf('Protobuf\Stream', $repeated->getBytesList()[1]);
        $this->assertInstanceOf('Protobuf\Stream', $repeated->getBytesList()[2]);

        $this->assertEquals('bin1', $repeated->getBytesList()[0]);
        $this->assertEquals('bin2', $repeated->getBytesList()[1]);
        $this->assertEquals('bin3', $repeated->getBytesList()[2]);
    }

    public function testReadRepeatedEnum()
    {
        $binary   = $this->getProtoContent('repeated-enum.bin');
        $repeated = Repeated::fromStream($binary);

        $this->assertInstanceOf(Repeated::CLASS, $repeated);
        $this->assertInstanceOf(Collection::CLASS, $repeated->getEnumList());
        $this->assertCount(2, $repeated->getEnumList());

        $this->assertInstanceOf('Protobuf\Enum', $repeated->getEnumList()[0]);
        $this->assertInstanceOf('Protobuf\Enum', $repeated->getEnumList()[1]);

        $this->assertSame(Repeated\Enum::FOO(), $repeated->getEnumList()[0]);
        $this->assertSame(Repeated\Enum::BAR(), $repeated->getEnumList()[1]);
    }

    public function testReadComplexMessage()
    {
        $binary  = $this->getProtoContent('addressbook.bin');
        $complex = AddressBook::fromStream($binary);

        $this->assertInstanceOf(AddressBook::CLASS, $complex);
        $this->assertCount(2, $complex->getPersonList());

        $person1 = $complex->getPersonList()[0];
        $person2 = $complex->getPersonList()[1];

        $this->assertInstanceOf(Person::CLASS, $person1);
        $this->assertInstanceOf(Person::CLASS, $person2);

        $this->assertEquals($person1->getId(), 2051);
        $this->assertEquals($person1->getName(), 'John Doe');

        $this->assertEquals($person2->getId(), 23);
        $this->assertEquals($person2->getName(), 'Iván Montes');

        $this->assertCount(2, $person1->getPhoneList());
        $this->assertCount(1, $person2->getPhoneList());

        $this->assertEquals($person1->getPhoneList()[0]->getNumber(), '1231231212');
        $this->assertEquals($person1->getPhoneList()[0]->getType(), PhoneType::HOME());

        $this->assertEquals($person1->getPhoneList()[1]->getNumber(), '55512321312');
        $this->assertEquals($person1->getPhoneList()[1]->getType(), PhoneType::MOBILE());

        $this->assertEquals($person2->getPhoneList()[0]->getNumber(), '3493123123');
        $this->assertEquals($person2->getPhoneList()[0]->getType(), PhoneType::WORK());
    }

    public function testReadPhpOptionsMessage()
    {
        $binary  = $this->getProtoContent('php_options.bin');
        $message = ParentMessage::fromStream($binary);

        $this->assertInstanceOf(ParentMessage::CLASS, $message);
        $this->assertCount(2, $message->getInnerList());
        $this->assertSame(ParentMessage\InnerEnum::VALUE1(), $message->getEnum());

        $inner1 = $message->getInnerList()[0];
        $inner2 = $message->getInnerList()[1];

        $this->assertInstanceOf(ParentMessage\InnerMessage::CLASS, $inner1);
        $this->assertInstanceOf(ParentMessage\InnerMessage::CLASS, $inner2);

        $this->assertSame(ParentMessage\InnerMessage\InnerMessageEnum::VALUE1(), $inner1->getEnum());
        $this->assertSame(ParentMessage\InnerMessage\InnerMessageEnum::VALUE2(), $inner2->getEnum());
    }

    public function testReadTreeMessage()
    {
        $binary = $this->getProtoContent('tree.bin');
        $root   = Tree\Node::fromStream($binary);

        $this->assertInstanceOf(Tree\Node::CLASS, $root);
        $this->assertCount(2, $root->getChildrenList());
        $this->assertEquals($root->getPath(), '/Users');

        $node1 = $root->getChildrenList()[0];
        $node2 = $root->getChildrenList()[1];

        $this->assertInstanceOf(Tree\Node::CLASS, $node1);
        $this->assertInstanceOf(Tree\Node::CLASS, $node2);

        $this->assertEquals('/Users/fabio', $node1->getPath());
        $this->assertEquals('/Users/admin', $node2->getPath());

        $this->assertInstanceOf(Tree\Node::CLASS, $node1->getParent());
        $this->assertInstanceOf(Tree\Node::CLASS, $node2->getParent());

        $this->assertEquals('/Users', $node1->getParent()->getPath());
        $this->assertEquals('/Users', $node2->getParent()->getPath());
    }

    public function testReadExtensionAnimalMessage()
    {
        Extension\Extension::registerAllExtensions($this->config->getExtensionRegistry());

        $binary = $this->getProtoContent('extension-animal-cat.bin');
        $animal = Extension\Animal::fromStream($binary, $this->config);

        $this->assertInstanceOf(Extension\Animal::CLASS, $animal);
        $this->assertInstanceOf(Collection::CLASS, $animal->extensions());
        $this->assertEquals(Extension\Animal\Type::CAT(), $animal->getType());

        $extensions = $animal->extensions();
        $cat        = $extensions->get(Extension\Cat::animal());

        $this->assertInstanceOf(Extension\Cat::CLASS, $cat);
        $this->assertTrue($cat->getDeclawed());
    }

    public function testReadExtensionCommandMessage()
    {
        Extension\Extension::registerAllExtensions($this->config->getExtensionRegistry());

        $binary = $this->getProtoContent('extension-command-version.bin');
        $command = Extension\Command::fromStream($binary, $this->config);

        $this->assertInstanceOf(Extension\Command::CLASS, $command);
        $this->assertInstanceOf(Collection::CLASS, $command->extensions());
        $this->assertEquals(Extension\Command\CommandType::VERSION(), $command->getType());

        $extensions = $command->extensions();
        $verbose    = $extensions->get(Extension\Extension::verbose());
        $version    = $extensions->get(Extension\VersionCommand::cmd());

        $this->assertTrue($verbose);
        $this->assertInstanceOf(Extension\VersionCommand::CLASS, $version);
        $this->assertEquals(1, $version->getVersion());
        $this->assertSame(Extension\VersionCommand\Protocol::V1(), $version->getProtocol());
    }

    public function testUnknownFieldSet()
    {
        $binary       = $this->getProtoContent('unknown.bin');
        $unrecognized = Unrecognized::fromStream(Stream::wrap($binary));

        $this->assertInstanceOf(Unrecognized::CLASS, $unrecognized);
        $this->assertInstanceOf('Protobuf\UnknownFieldSet', $unrecognized->unknownFieldSet());
        $this->assertCount(15, $unrecognized->unknownFieldSet());

        $values = $unrecognized->unknownFieldSet();

        $this->assertInstanceOf('Protobuf\Unknown', $values[1]);
        $this->assertInstanceOf('Protobuf\Unknown', $values[2]);
        $this->assertInstanceOf('Protobuf\Unknown', $values[3]);
        $this->assertInstanceOf('Protobuf\Unknown', $values[4]);
        $this->assertInstanceOf('Protobuf\Unknown', $values[5]);
        $this->assertInstanceOf('Protobuf\Unknown', $values[6]);
        $this->assertInstanceOf('Protobuf\Unknown', $values[7]);
        $this->assertInstanceOf('Protobuf\Unknown', $values[8]);
        $this->assertInstanceOf('Protobuf\Unknown', $values[9]);
        $this->assertInstanceOf('Protobuf\Unknown', $values[12]);
        $this->assertInstanceOf('Protobuf\Unknown', $values[13]);
        $this->assertInstanceOf('Protobuf\Unknown', $values[15]);
        $this->assertInstanceOf('Protobuf\Unknown', $values[16]);
        $this->assertInstanceOf('Protobuf\Unknown', $values[17]);
        $this->assertInstanceOf('Protobuf\Unknown', $values[18]);

        $this->assertEquals(4728057454355442093, $values[1]->value);
        $this->assertEquals(1178657918, $values[2]->value);
        $this->assertEquals(-123456789123456789, $values[3]->value);
        $this->assertEquals(123456789123456789, $values[4]->value);
        $this->assertEquals(-123456789, $values[5]->value);
        $this->assertEquals(123456789123456789, $values[6]->value);
        $this->assertEquals(123456789, $values[7]->value);
        $this->assertEquals(1, $values[8]->value);
        $this->assertEquals("foo", $values[9]->value);
        $this->assertEquals("bar", $values[12]->value);
        $this->assertEquals(123456789, $values[13]->value);
        $this->assertEquals(4171510507, $values[15]->value);
        $this->assertEquals(-123456789123456789, $values[16]->value);
        $this->assertEquals(246913577, $values[17]->value);
        $this->assertEquals(246913578246913577, $values[18]->value);
    }
}
