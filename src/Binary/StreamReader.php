<?php

namespace Protobuf\Binary;

use Protobuf\Binary\Platform\BigEndian;
use Protobuf\MessageInterface;
use Protobuf\Configuration;
use Protobuf\WireFormat;
use Protobuf\Unknown;
use Protobuf\Stream;
use RuntimeException;

/**
 * Implements reading primitives for Protobuf binary streams.
 *
 * @author Iván Montes <drslump@pollinimini.net>
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StreamReader
{
    /**
     * @var \Protobuf\Configuration
     */
    protected $config;

    /**
     * @var \Protobuf\Stream
     */
    protected $stream;

    /**
     * @var bool
     */
    protected $isBigEndian;

    /**
     * Constructor
     *
     * @param \Protobuf\Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config      = $config;
        $this->isBigEndian = BigEndian::isBigEndian();
    }

    /**
     * Reads a byte.
     *
     * @param \Protobuf\Stream $stream
     *
     * @return integer
     */
    public function readByte(Stream $stream)
    {
        $char = $stream->read(1);
        $byte = ord($char);

        return $byte;
    }

    /**
     * Decode a varint.
     *
     * @param \Protobuf\Stream $stream
     *
     * @return integer
     */
    public function readVarint(Stream $stream)
    {
        // Optimize common case (single byte varints)
        $byte = $this->readByte($stream);

        if ($byte < 0x80) {
            return $byte;
        }

        $length = $stream->getSize();
        $offset = $stream->tell();
        $result = $byte & 0x7f;
        $shift  = 7;

        // fastpath 32bit varints (5bytes) by unrolling the loop
        if ($length - $offset >= 4) {
            // 2
            $byte    = $this->readByte($stream);
            $result |= ($byte & 0x7f) << 7;

            if ($byte < 0x80) {
                return $result;
            }

            // 3
            $byte    = $this->readByte($stream);
            $result |= ($byte & 0x7f) << 14;

            if ($byte < 0x80) {
                return $result;
            }

            // 4
            $byte    = $this->readByte($stream);
            $result |= ($byte & 0x7f) << 21;

            if ($byte < 0x80) {
                return $result;
            }

            // 5
            $byte    = $this->readByte($stream);
            $result |= ($byte & 0x7f) << 28;

            if ($byte < 0x80) {
                return $result;
            }

            $shift = 35;
        }

        // If we're just at the end of the buffer or handling a 64bit varint
        do {
            $byte    = $this->readByte($stream);
            $result |= ($byte & 0x7f) << $shift;
            $shift  += 7;
        } while ($byte > 0x7f);

        return $result;
    }

    /**
     * Decodes a zigzag integer of the given bits.
     *
     * @param \Protobuf\Stream $stream
     *
     * @return integer
     */
    public function readZigzag(Stream $stream)
    {
        $number = $this->readVarint($stream);
        $zigzag = ($number >> 1) ^ (-($number & 1));

        return $zigzag;
    }

    /**
     * Decode a fixed 32bit integer with sign.
     *
     * @param \Protobuf\Stream $stream
     *
     * @return integer
     */
    public function readSFixed32(Stream $stream)
    {
        $bytes = $stream->read(4);

        if ($this->isBigEndian) {
            $bytes = strrev($bytes);
        }

        list(, $result) = unpack('l', $bytes);

        return $result;
    }

    /**
     * Decode a fixed 32bit integer without sign.
     *
     * @param \Protobuf\Stream $stream
     *
     * @return integer
     */
    public function readFixed32(Stream $stream)
    {
        $bytes = $stream->read(4);

        if (PHP_INT_SIZE < 8) {
            list(, $lo, $hi) = unpack('v*', $bytes);

            return $hi << 16 | $lo;
        }

        list(, $result) = unpack('V*', $bytes);

        return $result;
    }

    /**
     * Decode a fixed 64bit integer with sign.
     *
     * @param \Protobuf\Stream $stream
     *
     * @return integer
     */
    public function readSFixed64(Stream $stream)
    {
        $bytes = $stream->read(8);

        list(, $lo0, $lo1, $hi0, $hi1) = unpack('v*', $bytes);

        return ($hi1 << 16 | $hi0) << 32 | ($lo1 << 16 | $lo0);
    }

    /**
     * Decode a fixed 64bit integer without sign.
     *
     * @param \Protobuf\Stream $stream
     *
     * @return integer
     */
    public function readFixed64(Stream $stream)
    {
        return $this->readSFixed64($stream);
    }

    /**
     * Decode a 32bit float.
     *
     * @param \Protobuf\Stream $stream
     *
     * @return float
     */
    public function readFloat(Stream $stream)
    {
        $bytes = $stream->read(4);

        if ($this->isBigEndian) {
            $bytes = strrev($bytes);
        }

        list(, $result) = unpack('f', $bytes);

        return $result;
    }

    /**
     * Decode a 64bit double.
     *
     * @param \Protobuf\Stream $stream
     *
     * @return float
     */
    public function readDouble(Stream $stream)
    {
        $bytes = $stream->read(8);

        if ($this->isBigEndian) {
            $bytes = strrev($bytes);
        }

        list(, $result) = unpack('d', $bytes);

        return $result;
    }

    /**
     * Decode a bool.
     *
     * @param \Protobuf\Stream $stream
     *
     * @return bool
     */
    public function readBool(Stream $stream)
    {
        return (bool) $this->readVarint($stream);
    }

    /**
     * Decode a string.
     *
     * @param \Protobuf\Stream $stream
     *
     * @return string
     */
    public function readString(Stream $stream)
    {
        $length = $this->readVarint($stream);
        $string = $stream->read($length);

        return $string;
    }

    /**
     * Decode a stream of bytes.
     *
     * @param \Protobuf\Stream $stream
     *
     * @return \Protobuf\Stream
     */
    public function readByteStream(Stream $stream)
    {
        $length = $this->readVarint($stream);
        $value  = $stream->readStream($length);

        return $value;
    }

    /**
     * Read unknown scalar value.
     *
     * @param \Protobuf\Stream $stream
     * @param integer          $wire
     *
     * @return scalar
     */
    public function readUnknown(Stream $stream, $wire)
    {
        if ($wire === WireFormat::WIRE_VARINT) {
            return $this->readVarint($stream);
        }

        if ($wire === WireFormat::WIRE_LENGTH) {
            return $this->readString($stream);
        }

        if ($wire === WireFormat::WIRE_FIXED32) {
            return $this->readFixed32($stream);
        }

        if ($wire === WireFormat::WIRE_FIXED64) {
            return $this->readFixed64($stream);
        }

        if ($wire === WireFormat::WIRE_GROUP_START || $wire === WireFormat::WIRE_GROUP_END) {
            throw new RuntimeException('Groups are deprecated in Protocol Buffers and unsupported.');
        }

        throw new RuntimeException("Unsupported wire type '$wire' while reading unknown field.");
    }
}
