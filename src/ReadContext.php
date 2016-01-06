<?php

namespace Protobuf;

use Protobuf\Stream;
use Protobuf\Binary\StreamReader;
use Protobuf\Extension\ExtensionRegistry;

/**
 * Read context
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ReadContext
{
    /**
     * @var \Protobuf\Extension\ExtensionRegistry
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
     * @param \Protobuf\Stream|resource|string      $stream
     * @param \Protobuf\Binary\StreamReader         $reader
     * @param \Protobuf\Extension\ExtensionRegistry $extensionRegistry
     */
    public function __construct($stream, StreamReader $reader, ExtensionRegistry $extensionRegistry = null)
    {
        if ( ! $stream instanceof \Protobuf\Stream) {
            $stream = Stream::wrap($stream);
        }

        $this->stream            = $stream;
        $this->reader            = $reader;
        $this->extensionRegistry = $extensionRegistry;
    }

    /**
     * Return a ExtensionRegistry.
     *
     * @return \Protobuf\Extension\ExtensionRegistry
     */
    public function getExtensionRegistry()
    {
        return $this->extensionRegistry;
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
