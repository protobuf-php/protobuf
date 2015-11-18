<?php

namespace ProtobufTest\Binary\Platform;

use ProtobufTest\TestCase;
use Protobuf\Binary\Platform\InvalidNegativeEncoder;

class InvalidNegativeEncoderTest extends TestCase
{
    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Negative integers are only supported with GMP or BC (64bit) intextensions.
     */
    public function testEncodeVarintException()
    {
        $encoder = new InvalidNegativeEncoder();

        $encoder->encodeVarint(-1);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Negative integers are only supported with GMP or BC (64bit) intextensions.
     */
    public function testEncodeEncodeSFixed64Exception()
    {
        $encoder = new InvalidNegativeEncoder();

        $encoder->encodeSFixed64(-1);
    }
}