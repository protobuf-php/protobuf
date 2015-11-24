<?php

namespace Protobuf;

use Protobuf\Stream;
use Protobuf\Binary\StreamReader;

/**
 * Read context
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ReadContext
{
    /**
     * @var \Protobuf\Binary\StreamReader
     */
    private $extensionRegistry;

    /**
     * @var \Protobuf\Binary\StreamReader
     */
    private $reader;

    /**
     * @var \Protobuf\Stream
     */
    private $stream;

    /**
     * @var integer
     */
    private $length;

    /**
     * @param \Protobuf\Stream|resource|string $stream
     * @param \Protobuf\Binary\StreamReader    $reader
     * @param integer                          $length
     */
    public function __construct($stream, StreamReader $reader, $length = null)
    {
        if ( ! $stream instanceof \Protobuf\Stream) {
            $stream = Stream::create($stream);
        }

        $this->stream = $stream;
        $this->reader = $reader;
        $this->length = $length;
    }

    /**
     * Return a ExtensionRegistry.
     *
     * @return \Protobuf\ExtensionRegistry
     */
    public function getExtensionRegistry()
    {
        return $this->extensionRegistry;
    }

    /**
     * Set a ExtensionRegistry.
     *
     * @param \Protobuf\ExtensionRegistry $extensionRegistry
     */
    public function setExtensionRegistry(ExtensionRegistry $extensionRegistry)
    {
        $this->extensionRegistry = $extensionRegistry;
    }

    /**
     * @return \Protobuf\Binary\StreamReader
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * @return \Protobuf\Stream
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * @return integer
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param integer $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }
}
