<?php

namespace Protobuf\Binary\Platform;

use RuntimeException;

/**
 * BC math negative values enconding.
 *
 * @author Iván Montes <drslump@pollinimini.net>
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class BcNegativeEncoder implements NegativeEncoder
{
    /**
     * {@inheritdoc}
     */
    public function encodeVarint($varint)
    {
        $values = [];
        $value  = sprintf('%u', $varint);

        while (bccomp($value, 0, 0) > 0) {
            // Get the last 7bits of the number
            $bin = '';
            $dec = $value;

            do {
                $rest = bcmod($dec, 2);
                $dec  = bcdiv($dec, 2, 0);
                $bin  = $rest . $bin;
            } while ($dec > 0 && mb_strlen($bin, '8bit') < 7);

            // Pack as a decimal and apply the flag
            $values[] = intval($bin, 2) | 0x80;
            $value    = bcdiv($value, 0x80, 0);
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function encodeSFixed64($sFixed64)
    {
        $value = sprintf('%u', $sFixed64);
        $bytes = '';

        for ($i = 0; $i < 8; ++$i) {
            // Get the last 8bits of the number
            $bin = '';
            $dec = $value;

            do {
                $bin = bcmod($dec, 2).$bin;
                $dec = bcdiv($dec, 2, 0);
            } while (mb_strlen($bin, '8bit') < 8);

            // Pack the byte
            $bytes .= chr(intval($bin, 2));
            $value  = bcdiv($value, 0x100, 0);
        }

        return $bytes;
    }
}
