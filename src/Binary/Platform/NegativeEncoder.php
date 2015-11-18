<?php

namespace Protobuf\Binary\Platform;

/**
 * Implements platform specific encoding of negative values.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface NegativeEncoder
{
    /**
     * Encode a negative varint.
     *
     * @param integer $value
     *
     * @return array
     */
    public function encodeVarint($value);

    /**
     * Encode an integer as a fixed of 64bits.
     *
     * @param integer $value
     *
     * @return string
     */
    public function encodeSFixed64($value);
}
