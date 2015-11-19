<?php

namespace ProtobufTest\Binary\Platform;

use ProtobufTest\TestCase;
use Protobuf\Binary\Platform\PlatformFactory;

class PlatformFactoryTest extends TestCase
{
    public function testGetGmpNegativeEncoder()
    {
        if ( ! extension_loaded('gmp')) {
            $this->markTestSkipped('The GMP extension is not available.');
        }

        $factory = $this->getMockBuilder(PlatformFactory::CLASS)
            ->setMethods(['isExtensionLoaded'])
            ->getMock();

        $factory->expects($this->once())
            ->method('isExtensionLoaded')
            ->with($this->equalTo('gmp'))
            ->willReturn(true);

        $this->assertInstanceOf('Protobuf\Binary\Platform\GmpNegativeEncoder', $factory->getNegativeEncoder());
    }

    public function testGetBcNegativeEncoder()
    {
        if ( ! extension_loaded('bcmath')) {
            $this->markTestSkipped('The BC MATH extension is not available.');
        }

        $factory = $this->getMockBuilder(PlatformFactory::CLASS)
            ->setMethods(['isExtensionLoaded'])
            ->getMock();

        $factory->expects($this->exactly(2))
            ->method('isExtensionLoaded')
            ->will($this->returnValueMap([
                ['gmp', false],
                ['bcmath', true]
            ]));

        $this->assertInstanceOf('Protobuf\Binary\Platform\BcNegativeEncoder', $factory->getNegativeEncoder());
    }

    public function testGetInvalidNegativeEncoder()
    {
        $factory = $this->getMockBuilder(PlatformFactory::CLASS)
            ->setMethods(['isExtensionLoaded'])
            ->getMock();

        $factory->expects($this->exactly(2))
            ->method('isExtensionLoaded')
            ->will($this->returnValueMap([
                ['gmp', false],
                ['bcmath', false]
            ]));

        $this->assertInstanceOf('Protobuf\Binary\Platform\InvalidNegativeEncoder', $factory->getNegativeEncoder());
    }
}