<?php

namespace ProtobufTest\Extension;

use ProtobufTest\TestCase;
use ProtobufTest\Protos\Extension\Cat;
use ProtobufTest\Protos\Extension\Animal;

use Protobuf\ExtensionFieldMap;
use Protobuf\Extension;

class ExtensionFieldMapTest extends TestCase
{
    public function testPutAndGetExtensions()
    {
        $animal     = new Cat();
        $extensions = new ExtensionFieldMap(Animal::CLASS);
        $extension  = $this->getMock('\Protobuf\Extension');

        $extension->method('getTag')
            ->willReturn(100);

        $extension->method('getExtendee')
            ->willReturn(Animal::CLASS);

        $this->assertCount(0, $extensions);

        $extensions->put($extension, $animal);

        $this->assertCount(1, $extensions);
        $this->assertTrue($extensions->contains($extension));
        $this->assertSame($animal, $extensions->offsetGet($extension));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid extendee, ProtobufTest\Protos\Extension\Animal is expected but ProtobufTest\Protos\Extension\Cat given
     */
    public function testInvalidArgumentExceptionExtendee()
    {
        $animal     = new Cat();
        $extensions = new ExtensionFieldMap(Animal::CLASS);
        $extension  = $this->getMock('\Protobuf\Extension');

        $extension->method('getTag')
            ->willReturn(100);

        $extension->method('getExtendee')
            ->willReturn(Cat::CLASS);

        $extensions->put($extension, $animal);
    }
}
