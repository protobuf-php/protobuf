<?php

namespace ProtobufTest\Binary\Platform;

use ProtobufTest\TestCase;
use Protobuf\Binary\Platform\BcNegativeEncoder;

class BcNegativeEncoderTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        if ( ! extension_loaded('bcmath')) {
            $this->markTestSkipped('The BC MATH extension is not available.');
        }
    }

    public function testEncodeVarint()
    {
        $encoder  = new BcNegativeEncoder();
        $actual   = $encoder->encodeVarint(-10);
        $expected = [
            246, 255, 255, 255, 255,
            255, 255, 255, 255, 129,
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testEncodeSFixed64()
    {
        $encoder  = new BcNegativeEncoder();
        $bytes    = $encoder->encodeSFixed64(-123456789123456789);
        $expected = [
            1 => 41195,
            2 => 21295,
            3 => 25780,
            4 => 65097
        ];

        $this->assertEquals($expected, unpack('v*', $bytes));
    }
}