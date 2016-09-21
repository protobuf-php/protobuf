<?php

namespace Protobuf\Binary;

use Protobuf\Stream;
use Protobuf\WireFormat;
use Protobuf\Configuration;
use Protobuf\Binary\Platform\BigEndian;

/**
 * Implements writing primitives for Protobuf binary streams.
 *
 * @author Iván Montes <drslump@pollinimini.net>
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StreamWriter
{
    /**
     * @var \Protobuf\Configuration
     */
    protected $config;

    /**
     * @var \Protobuf\Binary\Platform\NegativeEncoder
     */
    protected $negativeEncoder;

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
        $this->config          = $config;
        $this->isBigEndian     = BigEndian::isBigEndian();
        $this->negativeEncoder = $config->getPlatformFactory()
            ->getNegativeEncoder();
    }

    /**
     * Store the given bytes in the stream.
     *
     * @param \Protobuf\Stream $stream
     * @param string           $bytes
     * @param int              $length
     */
    public function writeBytes(Stream $stream, $bytes, $length = null)
    {
        if ($length === null) {
            $length = mb_strlen($bytes, '8bit');
        }

        $stream->write($bytes, $length);
    }

    /**
     * Store a single byte.
     *
     * @param \Protobuf\Stream $stream
     * @param integer          $value
     */
    public function writeByte(Stream $stream, $value)
    {
        $stream->write(chr($value), 1);
    }

    /**
     * Store an integer encoded as varint.
     *
     * @param \Protobuf\Stream $stream
     * @param integer          $value
     */
    public function writeVarint(Stream $stream, $value)
    {
        // Small values do not need to be encoded
        if ($value >= 0 && $value < 0x80) {
            $this->writeByte($stream, $value);

            return;
        }

        $values = null;

        // Build an array of bytes with the encoded values
        if ($value > 0) {
            $values = [];

            while ($value > 0) {
                $values[] = 0x80 | ($value & 0x7f);
                $value    = $value >> 7;
            }
        }

        if ($values === null) {
            $values = $this->negativeEncoder->encodeVarint($value);
        }

        // Remove the MSB flag from the last byte
        $values[count($values) - 1] &= 0x7f;

        // Convert the byte sized ints to actual bytes in a string
        $values = array_merge(['C*'], $values);
        $bytes  = call_user_func_array('pack', $values);

        $this->writeBytes($stream, $bytes);
    }

    /**
     * Encodes an integer with zigzag.
     *
     * @param \Protobuf\Stream $stream
     * @param integer          $value
     * @param integer          $base
     */
    public function writeZigzag(Stream $stream, $value, $base = 32)
    {
        if ($base == 32) {
            $this->writeZigzag32($stream, $value);

            return;
        }

        $this->writeZigzag64($stream, $value);
    }

    /**
     * Encodes an integer with zigzag.
     *
     * @param \Protobuf\Stream $stream
     * @param integer          $value
     */
    public function writeZigzag32(Stream $stream, $value)
    {
        $this->writeVarint($stream, ($value << 1) ^ ($value >> 32 - 1));
    }

    /**
     * Encodes an integer with zigzag.
     *
     * @param \Protobuf\Stream $stream
     * @param integer          $value
     */
    public function writeZigzag64(Stream $stream, $value)
    {
        $this->writeVarint($stream, ($value << 1) ^ ($value >> 64 - 1));
    }

    /**
     * Encode an integer as a fixed of 32bits with sign.
     *
     * @param \Protobuf\Stream $stream
     * @param integer          $value
     */
    public function writeSFixed32(Stream $stream, $value)
    {
        $bytes = pack('l*', $value);

        if ($this->isBigEndian) {
            $bytes = strrev($bytes);
        }

        $this->writeBytes($stream, $bytes);
    }

    /**
     * Encode an integer as a fixed of 32bits without sign.
     *
     * @param \Protobuf\Stream $stream
     * @param integer          $value
     */
    public function writeFixed32(Stream $stream, $value)
    {
        $this->writeBytes($stream, pack('V*', $value), 4);
    }

    /**
     * Encode an integer as a fixed of 64bits with sign.
     *
     * @param \Protobuf\Stream $stream
     * @param integer          $value
     */
    public function writeSFixed64(Stream $stream, $value)
    {
        if ($value >= 0) {
            $this->writeFixed64($stream, $value);

            return;
        }

        $bytes = $this->negativeEncoder->encodeSFixed64($value);

        $this->writeBytes($stream, $bytes);
    }

    /**
     * Encode an integer as a fixed of 64bits without sign.
     *
     * @param \Protobuf\Stream $stream
     * @param integer          $value
     */
    public function writeFixed64(Stream $stream, $value)
    {
        $bytes = pack('V*', $value & 0xffffffff, $value / (0xffffffff + 1));

        $this->writeBytes($stream, $bytes, 8);
    }

    /**
     * Encode a number as a 32bit float.
     *
     * @param \Protobuf\Stream $stream
     * @param float            $value
     */
    public function writeFloat(Stream $stream, $value)
    {
        $bytes = pack('f*', $value);

        if ($this->isBigEndian) {
            $bytes = strrev($bytes);
        }

        $this->writeBytes($stream, $bytes, 4);
    }

    /**
     * Encode a number as a 64bit double.
     *
     * @param \Protobuf\Stream $stream
     * @param float            $value
     */
    public function writeDouble(Stream $stream, $value)
    {
        $bytes = pack('d*', $value);

        if ($this->isBigEndian) {
            $bytes = strrev($bytes);
        }

        $this->writeBytes($stream, $bytes, 8);
    }

    /**
     * Encode a bool.
     *
     * @param \Protobuf\Stream $stream
     * @param bool             $value
     */
    public function writeBool(Stream $stream, $value)
    {
        $this->writeVarint($stream, $value ? 1 : 0);
    }

    /**
     * Encode a string.
     *
     * @param \Protobuf\Stream $stream
     * @param string           $value
     */
    public function writeString(Stream $stream, $value)
    {
        $this->writeVarint($stream, mb_strlen($value, '8bit'));
        $this->writeBytes($stream, $value);
    }

    /**
     * Encode a stream of bytes.
     *
     * @param \Protobuf\Stream $stream
     * @param \Protobuf\Stream $value
     */
    public function writeByteStream(Stream $stream, Stream $value)
    {
        $length = $value->getSize();

        $value->seek(0);
        $this->writeVarint($stream, $length);
        $stream->writeStream($value, $length);
    }

    /**
     * Write the given stream.
     *
     * @param \Protobuf\Stream $stream
     * @param \Protobuf\Stream $value
     * @param int              $length
     */
    public function writeStream(Stream $stream, Stream $value, $length = null)
    {
        if ($length === null) {
            $length = $value->getSize();
        }

        $stream->writeStream($value, $length);
    }
}
