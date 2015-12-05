<?php

namespace Protobuf;

/**
 * Protobuf field label (optional, required, repeated) and types
 *
 * @author Iván Montes <drslump@pollinimini.net>
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class Field
{
    const LABEL_OPTIONAL = 1;
    const LABEL_REQUIRED = 2;
    const LABEL_REPEATED = 3;
    const LABEL_UNKNOWN  = -1;

    const TYPE_DOUBLE    = 1;
    const TYPE_FLOAT     = 2;
    const TYPE_INT64     = 3;
    const TYPE_UINT64    = 4;
    const TYPE_INT32     = 5;
    const TYPE_FIXED64   = 6;
    const TYPE_FIXED32   = 7;
    const TYPE_BOOL      = 8;
    const TYPE_STRING    = 9;
    const TYPE_GROUP     = 10;
    const TYPE_MESSAGE   = 11;
    const TYPE_BYTES     = 12;
    const TYPE_UINT32    = 13;
    const TYPE_ENUM      = 14;
    const TYPE_SFIXED32  = 15;
    const TYPE_SFIXED64  = 16;
    const TYPE_SINT32    = 17;
    const TYPE_SINT64    = 18;
    const TYPE_UNKNOWN   = -1;

    /**
     * @var array
     */
    protected static $names = [
        self::TYPE_DOUBLE   => 'double',
        self::TYPE_FLOAT    => 'float',
        self::TYPE_INT64    => 'int64',
        self::TYPE_UINT64   => 'uint64',
        self::TYPE_INT32    => 'int32',
        self::TYPE_FIXED64  => 'fixed64',
        self::TYPE_FIXED32  => 'fixed32',
        self::TYPE_BOOL     => 'bool',
        self::TYPE_STRING   => 'string',
        self::TYPE_MESSAGE  => 'message',
        self::TYPE_BYTES    => 'bytes',
        self::TYPE_UINT32   => 'uint32',
        self::TYPE_ENUM     => 'enum',
        self::TYPE_SFIXED32 => 'sfixed32',
        self::TYPE_SFIXED64 => 'sfixed64',
        self::TYPE_SINT32   => 'sint32',
        self::TYPE_SINT64   => 'sint64',
    ];

    /**
     * Obtain the label name (repeated, optional, required).
     *
     * @param string $label
     *
     * @return string
     */
    public static function getLabelName($label)
    {
        if ($label === self::LABEL_OPTIONAL) {
            return 'optional';
        }

        if ($label === self::LABEL_REQUIRED) {
            return 'required';
        }

        if ($label === self::LABEL_REPEATED) {
            return 'repeated';
        }

        return null;
    }

    /**
     * @param integer $type
     *
     * @return string
     */
    public static function getTypeName($type)
    {
        return isset(self::$names[$type])
            ? self::$names[$type]
            : null;
    }

    /**
     * @param integer $type
     *
     * @return string
     */
    public static function getPhpType($type)
    {
        switch ($type) {
            case self::TYPE_DOUBLE:
            case self::TYPE_FLOAT:
                return 'float';
            case self::TYPE_INT64:
            case self::TYPE_UINT64:
            case self::TYPE_INT32:
            case self::TYPE_FIXED64:
            case self::TYPE_FIXED32:
            case self::TYPE_UINT32:
            case self::TYPE_SFIXED32:
            case self::TYPE_SFIXED64:
            case self::TYPE_SINT32:
            case self::TYPE_SINT64:
                return 'int';
            case self::TYPE_BOOL:
                return 'bool';
            case self::TYPE_STRING:
                return 'string';
            case self::TYPE_BYTES:
                return '\Protobuf\Stream';
            default:
                return null;
        }
    }
}
