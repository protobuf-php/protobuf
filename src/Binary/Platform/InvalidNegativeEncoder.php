<?php

namespace Protobuf\Binary\Platform;

use RuntimeException;

/**
 * Invalid platform for negative values
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class InvalidNegativeEncoder implements NegativeEncoder
{
    /**
     * {@inheritdoc}
     */
    public function encodeVarint($varint)
    {
        throw new RuntimeException("Negative integers are only supported with GMP or BC (64bit) intextensions.");
    }

    /**
     * {@inheritdoc}
     */
    public function encodeSFixed64($sFixed64)
    {
        throw new RuntimeException("Negative integers are only supported with GMP or BC (64bit) intextensions.");
    }
}
