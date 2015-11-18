<?php

namespace Protobuf\Binary\Platform;

/**
 * Check current architecture
 *
 * @author Iván Montes <drslump@pollinimini.net>
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class BigEndian
{
    /**
     * @var bool
     */
    protected static $is32Bit;

    /**
     * @var integer
     */
    protected static $isBigEndian;

    /**
     * Check if the current architecture is Big Endian.
     *
     * @return bool
     */
    public static function isBigEndian()
    {
        if (self::$isBigEndian !== null) {
            return self::$isBigEndian;
        }

        list(, $result)    = unpack('L', pack('V', 1));
        self::$isBigEndian = $result !== 1;

        return self::$isBigEndian;
    }

    /**
     * @return bool
     */
    public static function is32Bit()
    {
        if (self::$is32Bit !== null) {
            self::$is32Bit;
        }

        self::$is32Bit = (PHP_INT_SIZE < 8);

        return self::$is32Bit;
    }
}
