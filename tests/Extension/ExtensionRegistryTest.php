<?php

namespace ProtobufTest\Extension;

use ProtobufTest\TestCase;
use ProtobufTest\Protos\Extension\Animal;

use Protobuf\Extension\ExtensionRegistry;
use Protobuf\Extension\ExtensionField;

class ExtensionRegistryTest extends TestCase
{
    public function testAddAndFindByFieldNumber()
    {
        $registry  = new ExtensionRegistry();
        $extension = new ExtensionField(Animal::CLASS, 'animal', 100, function () {}, function () {}, function () {});

        $this->assertNull($registry->findByNumber(Animal::CLASS, 100));

        $registry->add($extension);

        $this->assertSame($extension, $registry->findByNumber(Animal::CLASS, 100));

        $registry->clear();

        $this->assertNull($registry->findByNumber(Animal::CLASS, 100));
    }
}