<?php

namespace Protobuf\Binary;

use Protobuf\Binary\Platform\BigEndian;
use Protobuf\MessageInterface;
use Protobuf\Configuration;
use Protobuf\WireFormat;
use Protobuf\Unknown;
use Protobuf\Stream;

/**
 * Compute the number of bytes that would be needed to encode a value
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class SizeCalculator
{
    /**
     * @var \Protobuf\Configuration
     */
    protected $config;

    /**
     * Constructor
     *
     * @param \Protobuf\Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * Compute the number of bytes that would be needed to encode a varint.
     *
     * @param integer $value
     *
     * @return integer
     */
    public function computeVarintSize($value)
    {
        if (($value & (0xffffffff <<  7)) === 0) {
            return 1;
        }

        if (($value & (0xffffffff << 14)) === 0) {
            return 2;
        }

        if (($value & (0xffffffff << 21)) === 0) {
            return 3;
        }

        if (($value & (0xffffffff << 28)) === 0) {
            return 4;
        }

        if (($value & (0xffffffff << 35)) === 0) {
            return 5;
        }

        if (($value & (0xffffffff << 42)) === 0) {
            return 6;
        }

        if (($value & (0xffffffff << 49)) === 0) {
            return 7;
        }

        if (($value & (0xffffffff << 56)) === 0) {
            return 8;
        }

        if (($value & (0xffffffff << 63)) === 0) {
            return 9;
        }

        return 10;
    }

    /**
     * Compute the number of bytes that would be needed to encode a zigzag 32.
     *
     * @param integer $value
     *
     * @return integer
     */
    public function computeZigzag32Size($value)
    {
        $varint = ($value << 1) ^ ($value >> 32 - 1);
        $size   = $this->computeVarintSize($varint);

        return $size;
    }

    /**
     * Compute the number of bytes that would be needed to encode a zigzag 64.
     *
     * @param integer $value
     *
     * @return integer
     */
    public function computeZigzag64Size($value)
    {
        $varint = ($value << 1) ^ ($value >> 64 - 1);
        $size   = $this->computeVarintSize($varint);

        return $size;
    }

    /**
     * Compute the number of bytes that would be needed to encode a string.
     *
     * @param integer $value
     *
     * @return integer
     */
    public function computeStringSize($value)
    {
        $length = mb_strlen($value, '8bit');
        $size   = $length + $this->computeVarintSize($length);

        return $size;
    }

    /**
     * Compute the number of bytes that would be needed to encode a stream of bytes.
     *
     * @param \Protobuf\Stream $value
     *
     * @return integer
     */
    public function computeByteStreamSize(Stream $value)
    {
        $length = $value->getSize();
        $size   = $length + $this->computeVarintSize($length);

        return $size;
    }

    /**
     * Compute the number of bytes that would be needed to encode a sFixed32.
     *
     * @return integer
     */
    public function computeSFixed32Size()
    {
        return 4;
    }

    /**
     * Compute the number of bytes that would be needed to encode a fixed32.
     *
     * @return integer
     */
    public function computeFixed32Size()
    {
        return 4;
    }

    /**
     * Compute the number of bytes that would be needed to encode a sFixed64.
     *
     * @return integer
     */
    public function computeSFixed64Size()
    {
        return 8;
    }

    /**
     * Compute the number of bytes that would be needed to encode a fixed64.
     *
     *
     * @return integer
     */
    public function computeFixed64Size()
    {
        return 8;
    }

    /**
     * Compute the number of bytes that would be needed to encode a float.
     *
     * @return integer
     */
    public function computeFloatSize()
    {
        return 4;
    }

    /**
     * Compute the number of bytes that would be needed to encode a double.
     *
     * @return integer
     */
    public function computeDoubleSize()
    {
        return 8;
    }

    /**
     * Compute the number of bytes that would be needed to encode a bool.
     *
     * @return integer
     */
    public function computeBoolSize()
    {
        return 1;
    }
}
