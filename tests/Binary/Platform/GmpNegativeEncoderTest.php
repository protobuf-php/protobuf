<?php

namespace ProtobufTest\Binary\Platform;

use ProtobufTest\TestCase;
use Protobuf\Binary\Platform\BigEndian;
use Protobuf\Binary\Platform\GmpNegativeEncoder;

class GmpNegativeEncoderTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        if ( ! extension_loaded('gmp') || ! function_exists('gmp_intval')) {
            $this->markTestSkipped('The GMP extension is not available.');
        }
    }

    public function testConstructInitializeGmpValues()
    {
        $encoder  = new GmpNegativeEncoder();
        $gmp_x00  = $this->getPropertyValue($encoder, 'gmp_x00');
        $gmp_x7f  = $this->getPropertyValue($encoder, 'gmp_x7f');
        $gmp_x80  = $this->getPropertyValue($encoder, 'gmp_x80');
        $gmp_xff  = $this->getPropertyValue($encoder, 'gmp_xff');
        $gmp_x100 = $this->getPropertyValue($encoder, 'gmp_x100');
        $is32Bit  = $this->getPropertyValue($encoder, 'is32Bit');

        $this->assertNotNull($gmp_x00);
        $this->assertNotNull($gmp_x7f);
        $this->assertNotNull($gmp_x80);
        $this->assertNotNull($gmp_xff);
        $this->assertNotNull($gmp_x100);

        $this->assertEquals(0, gmp_intval($gmp_x00));
        $this->assertEquals(127, gmp_intval($gmp_x7f));
        $this->assertEquals(128, gmp_intval($gmp_x80));
        $this->assertEquals(255, gmp_intval($gmp_xff));
        $this->assertEquals(256, gmp_intval($gmp_x100));
        $this->assertEquals(BigEndian::is32Bit(), $is32Bit);
    }

    public function testEncodeVarint()
    {
        $encoder  = new GmpNegativeEncoder();

        // make sure runs as 64 bit
        $this->setPropertyValue($encoder, 'is32Bit', false);

        $actual   = $encoder->encodeVarint(-10);
        $expected = [
            246, 255, 255, 255, 255,
            255, 255, 255, 255, 129,
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testEncodeSFixed64()
    {
        $encoder  = new GmpNegativeEncoder();

        // make sure runs as 64 bit
        $this->setPropertyValue($encoder, 'is32Bit', false);

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