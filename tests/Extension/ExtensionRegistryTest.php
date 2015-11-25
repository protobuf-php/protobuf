<?php

namespace ProtobufTest\Extension;

use ProtobufTest\TestCase;
use ProtobufTest\Protos\Extension\Animal;

use Protobuf\ExtensionRegistry;
use Protobuf\Extension;

class ExtensionRegistryTest extends TestCase
{
    public function testAddAndFindByFieldNumber()
    {
        $registry  = new ExtensionRegistry();
        $extension = $this->getMock('\Protobuf\Extension');

        $extension->method('getTag')
            ->willReturn(100);

        $extension->method('getExtendee')
            ->willReturn(Animal::CLASS);

        $this->assertNull($registry->findByNumber(Animal::CLASS, 100));

        $registry->add($extension);

        $this->assertSame($extension, $registry->findByNumber(Animal::CLASS, 100));

        $registry->clear();

        $this->assertNull($registry->findByNumber(Animal::CLASS, 100));
    }
}
