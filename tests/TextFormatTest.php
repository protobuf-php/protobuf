<?php

namespace ProtobufTest;

use Protobuf\Stream;
use Protobuf\TextFormat;
use ProtobufTest\TestCase;
use ProtobufTest\Protos\Tree;
use ProtobufTest\Protos\Simple;
use ProtobufTest\Protos\Person;
use ProtobufTest\Protos\Repeated;
use ProtobufTest\Protos\AddressBook;

class TextFormatTest extends TestCase
{
    /**
     * @var \Protobuf\TextFormat
     */
    private $textFormat;

    public function setUp()
    {
        parent::setUp();

        $this->textFormat = new TextFormat($this->config);
    }

    public function testFormatSimple()
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

        $expected = $this->getProtoContent('simple.txt');
        $actual   = $this->textFormat->encodeMessage($simple);

        $this->assertEquals($expected, (string) $actual);
    }

    public function testFormatRepeatedString()
    {
        $repeated = new Repeated();

        $repeated->addString('one');
        $repeated->addString('two');
        $repeated->addString('three');

        $expected = $this->getProtoContent('repeated-string.txt');
        $actual   = $this->textFormat->encodeMessage($repeated);

        $this->assertEquals($expected, (string) $actual);
    }

    public function testFormatRepeatedInt()
    {
        $repeated = new Repeated();

        $repeated->addInt(1);
        $repeated->addInt(2);
        $repeated->addInt(3);

        $expected = $this->getProtoContent('repeated-int32.txt');
        $actual   = $this->textFormat->encodeMessage($repeated);

        $this->assertEquals($expected, (string) $actual);
    }

    public function testFormatRepeatedNested()
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

        $expected = $this->getProtoContent('repeated-nested.txt');
        $actual   = $this->textFormat->encodeMessage($repeated);

        $this->assertEquals($expected, (string) $actual);
    }

    public function testFormatComplexMessage()
    {
        $book   = new AddressBook();
        $person = new Person();

        $person->setId(2051);
        $person->setName('John Doe');
        $person->setEmail('john.doe@gmail.com');

        $phone = new Person\PhoneNumber();

        $phone->setNumber('1231231212');
        $phone->setType(Person\PhoneType::HOME());

        $person->addPhone($phone);

        $phone = new Person\PhoneNumber();

        $phone->setNumber('55512321312');
        $phone->setType(Person\PhoneType::MOBILE());

        $person->addPhone($phone);
        $book->addPerson($person);

        $person = new Person();

        $person->setId(23);
        $person->setName('Iván Montes');
        $person->setEmail('drslump@pollinimini.net');

        $phone = new Person\PhoneNumber();

        $phone->setNumber('3493123123');
        $phone->setType(Person\PhoneType::WORK());

        $person->addPhone($phone);
        $book->addPerson($person);

        $expected = $this->getProtoContent('addressbook.txt');
        $actual   = $this->textFormat->encodeMessage($book);

        $this->assertEquals($expected, (string) $actual);
    }

    public function testFormatTreeMessage()
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

        $expected = $this->getProtoContent('tree.txt');
        $actual   = $root->__toString();

        $this->assertEquals($expected, (string) $actual);
    }

    public function testFormatTotring()
    {
        $repeated = new Repeated();

        $repeated->addString('one');
        $repeated->addString('two');
        $repeated->addString('three');

        $expected = $this->getProtoContent('repeated-string.txt');
        $actual   = $repeated->__toString();

        $this->assertEquals($expected, (string) $actual);
    }
}
