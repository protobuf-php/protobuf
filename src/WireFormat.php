<?php

namespace Protobuf;

use RuntimeException;

/**
 * This class contains constants and helper functions useful for dealing with
 * the Protocol Buffer wire format.
 *
 * @author Iván Montes <drslump@pollinimini.net>
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class WireFormat
{
    const WIRE_VARINT       = 0;
    const WIRE_FIXED64      = 1;
    const WIRE_LENGTH       = 2;
    const WIRE_GROUP_START  = 3;
    const WIRE_GROUP_END    = 4;
    const WIRE_FIXED32      = 5;
    const WIRE_UNKNOWN      = -1;

    const TAG_TYPE_BITS = 3;
    const TAG_TYPE_MASK = 0x7;

    /**
     * @var array
     */
    private static $wireTypeMap = [
        Field::TYPE_INT32    => WireFormat::WIRE_VARINT,
        Field::TYPE_INT64    => WireFormat::WIRE_VARINT,
        Field::TYPE_UINT32   => WireFormat::WIRE_VARINT,
        Field::TYPE_UINT64   => WireFormat::WIRE_VARINT,
        Field::TYPE_SINT32   => WireFormat::WIRE_VARINT,
        Field::TYPE_SINT64   => WireFormat::WIRE_VARINT,
        Field::TYPE_BOOL     => WireFormat::WIRE_VARINT,
        Field::TYPE_ENUM     => WireFormat::WIRE_VARINT,
        Field::TYPE_FIXED64  => WireFormat::WIRE_FIXED64,
        Field::TYPE_SFIXED64 => WireFormat::WIRE_FIXED64,
        Field::TYPE_DOUBLE   => WireFormat::WIRE_FIXED64,
        Field::TYPE_STRING   => WireFormat::WIRE_LENGTH,
        Field::TYPE_BYTES    => WireFormat::WIRE_LENGTH,
        Field::TYPE_MESSAGE  => WireFormat::WIRE_LENGTH,
        Field::TYPE_FIXED32  => WireFormat::WIRE_FIXED32,
        Field::TYPE_SFIXED32 => WireFormat::WIRE_FIXED32,
        Field::TYPE_FLOAT    => WireFormat::WIRE_FIXED32,
    ];

    /**
     * Given a field type, determines the wire type.
     *
     * @param integer $type
     * @param integer $default
     *
     * @return integer
     */
    public static function getWireType($type, $default)
    {
        // Unknown types just return the reported wire type
        return isset(self::$wireTypeMap[$type])
            ? self::$wireTypeMap[$type]
            : $default;
    }

    /**
     * Assert the wire type match
     *
     * @param integer $wire
     * @param integer $type
     */
    public static function assertWireType($wire, $type)
    {
        $expected = WireFormat::getWireType($type, $wire);

        if ($wire !== $expected) {
            throw new RuntimeException(sprintf(
                "Expected wire type %s but got %s for type %s.",
                $expected,
                $wire,
                $type
            ));
        }
    }

    /**
     * Given a tag value, determines the field number (the upper 29 bits).
     *
     * @param integer $tag
     *
     * @return integer
     */
    public static function getTagFieldNumber($tag)
    {
        return $tag >> self::TAG_TYPE_BITS;
    }

    /**
     * Given a tag value, determines the wire type (the lower 3 bits).
     *
     * @param integer $tag
     *
     * @return integer
     */
    public static function getTagWireType($tag)
    {
        return $tag & self::TAG_TYPE_MASK;
    }

    /**
     * Makes a tag value given a field number and wire type
     *
     * @param integer $tag
     * @param integer $wireType
     *
     * @return integer
     */
    public static function getFieldKey($tag, $wireType)
    {
        return ($tag << self::TAG_TYPE_BITS) | $wireType;
    }
}
