<?php

namespace ProtobufTest\Extension;

use ProtobufTest\TestCase;
use ProtobufTest\Protos\Extension\Cat;
use ProtobufTest\Protos\Extension\Animal;

use Protobuf\Extension\ExtensionFieldMap;
use Protobuf\Extension\ExtensionField;

class ExtensionFieldMapTest extends TestCase
{
    public function testPutAndGetExtensions()
    {
        $animal     = new Cat();
        $extensions = new ExtensionFieldMap(Animal::CLASS);
        $extension  = new ExtensionField(Animal::CLASS, 'animal', 100, function () {}, function () {}, function () {});

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
        $extension  = new ExtensionField(Cat::CLASS, 'animal', 200, function () {}, function () {}, function () {});

        $extensions->put($extension, $animal);
    }
}