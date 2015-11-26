<?php

namespace ProtobufTest;

use Protobuf\Configuration;

class ConfigurationTest extends TestCase
{
    /**
     * @var \Protobuf\Configuration
     */
    protected $configuration;

    protected function setUp()
    {
        $this->configuration = new Configuration();
    }

    public function testGetAndSetPlatformFactory()
    {
        $mock    = $this->getMock('Protobuf\Binary\Platform\PlatformFactory');
        $factory = $this->configuration->getPlatformFactory();

        $this->assertInstanceOf('Protobuf\Binary\Platform\PlatformFactory', $factory);

        $this->configuration->setPlatformFactory($mock);

        $this->assertSame($mock, $this->configuration->getPlatformFactory());
    }

    public function testGetAndSetExtensionRegistry()
    {
        $mock     = $this->getMock('Protobuf\Extension\ExtensionRegistry');
        $registry = $this->configuration->getExtensionRegistry();

        $this->assertInstanceOf('Protobuf\Extension\ExtensionRegistry', $registry);
        $this->assertSame($registry, $this->configuration->getExtensionRegistry());

        $this->configuration->setExtensionRegistry($mock);

        $this->assertSame($mock, $this->configuration->getExtensionRegistry());
    }
}
