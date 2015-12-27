<?php

namespace ProtobufTest;

use Protobuf\Enum;

class EnumTest extends TestCase
{
    public function testEnumNameAndValue()
    {
        $mock = $this->getMockBuilder(Enum::CLASS)
            ->setConstructorArgs(['FOO', 1])
            ->setMethods(['FOO'])
            ->getMock();

        $this->assertEquals(1, $mock->value());
        $this->assertEquals('FOO', $mock->name());
        $this->assertEquals('FOO', $mock->__toString());
    }
}
