<?php

namespace ProtobufTest\Binary\Platform;

use ProtobufTest\TestCase;
use Protobuf\Binary\Platform\BigEndian;

class BigEndianTest extends TestCase
{
    public function testIsBigEndian()
    {
        list(, $result)   = unpack('L', pack('V', 1));
        $actual           = BigEndian::isBigEndian();
        $expected         = ($result !== 1);

        $this->assertEquals($expected, $actual);
    }
}