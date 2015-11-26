<?php

namespace Protobuf;

use Protobuf\Binary\Platform\PlatformFactory;
use Protobuf\Extension\ExtensionRegistry;
use Protobuf\Binary\SizeCalculator;
use Protobuf\Binary\StreamWriter;
use Protobuf\Binary\StreamReader;

/**
 * Base configuration class for the protobuf
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class Configuration
{
    /**
     * @var \Protobuf\Extension\ExtensionRegistry
     */
    private $extensionRegistry;

    /**
     * @var \Protobuf\Binary\Platform\PlatformFactory
     */
    private $platformFactory;

    /**
     * @var \Protobuf\Binary\StreamWriter
     */
    private $streamWriter;

    /**
     * @var \Protobuf\Binary\StreamReader
     */
    private $streamReader;

    /**
     * @var \Protobuf\Binary\SizeCalculator
     */
    private $sizeCalculator;

    /**
     * @var \Protobuf\DescriptorLoader
     */
    protected static $instance;

    /**
     * Return a ExtensionRegistry.
     *
     * @return \Protobuf\Extension\ExtensionRegistry
     */
    public function getExtensionRegistry()
    {
        if ($this->extensionRegistry === null) {
            $this->extensionRegistry = new ExtensionRegistry();
        }

        return $this->extensionRegistry;
    }

    /**
     * Set a ExtensionRegistry.
     *
     * @param \Protobuf\Extension\ExtensionRegistry $extensionRegistry
     */
    public function setExtensionRegistry(ExtensionRegistry $extensionRegistry)
    {
        $this->extensionRegistry = $extensionRegistry;
    }

    /**
     * Return a PlatformFactory.
     *
     * @return \Protobuf\Binary\Platform\PlatformFactory
     */
    public function getPlatformFactory()
    {
        if ($this->platformFactory !== null) {
            return $this->platformFactory;
        }

        return $this->platformFactory = new PlatformFactory();
    }

    /**
     * Return a StreamReader
     *
     * @return \Protobuf\Binary\StreamReader
     */
    public function getStreamReader()
    {
        if ($this->streamReader !== null) {
            return $this->streamReader;
        }

        return $this->streamReader = new StreamReader($this);
    }

    /**
     * Return a StreamWriter
     *
     * @return \Protobuf\Binary\StreamWriter
     */
    public function getStreamWriter()
    {
        if ($this->streamWriter !== null) {
            return $this->streamWriter;
        }

        return $this->streamWriter = new StreamWriter($this);
    }

    /**
     * Return a SizeCalculator
     *
     * @return \Protobuf\Binary\SizeCalculator
     */
    public function getSizeCalculator()
    {
        if ($this->sizeCalculator !== null) {
            return $this->sizeCalculator;
        }

        return $this->sizeCalculator = new SizeCalculator($this);
    }

    /**
     * Sets the PlatformFactory.
     *
     * @param \Protobuf\Binary\Platform\PlatformFactory $platformFactory
     */
    public function setPlatformFactory(PlatformFactory $platformFactory)
    {
        $this->platformFactory = $platformFactory;
    }

    /**
     * Create a compute size context.
     *
     * @return \Protobuf\ComputeSizeContext
     */
    public function createComputeSizeContext()
    {
        $calculator = $this->getSizeCalculator();
        $context    = new ComputeSizeContext($calculator);

        return $context;
    }

    /**
     * Create a write context.
     *
     * @return \Protobuf\WriteContext
     */
    public function createWriteContext()
    {
        $stream      = Stream::create();
        $writer      = $this->getStreamWriter();
        $sizeContext = $this->createComputeSizeContext();
        $context     = new WriteContext($stream, $writer, $sizeContext);

        return $context;
    }

    /**
     * Create a read context.
     *
     * @param \Protobuf\Stream|resource|string $stream
     *
     * @return \Protobuf\ReadContext
     */
    public function createReadContext($stream)
    {
        $reader   = $this->getStreamReader();
        $registry = $this->extensionRegistry;
        $context  = new ReadContext($stream, $reader, $registry);

        return $context;
    }

    /**
     * Returns single instance of this class
     *
     * @return \Protobuf\Configuration
     */
    public static function getInstance()
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        return self::$instance = new Configuration();
    }
}
