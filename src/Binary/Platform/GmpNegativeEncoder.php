<?php

namespace Protobuf\Binary\Platform;

/**
 * GMP negative values enconding.
 *
 * @author Iván Montes <drslump@pollinimini.net>
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class GmpNegativeEncoder implements NegativeEncoder
{
    /**
     * @var \GMP
     */
    protected $gmp_x00;

    /**
     * @var \GMP
     */
    protected $gmp_x7f;

    /**
     * @var \GMP
     */
    protected $gmp_x80;

    /**
     * @var \GMP
     */
    protected $gmp_xff;

    /**
     * @var \GMP
     */
    protected $gmp_x100;

    /**
     * @var bool
     */
    protected $is32Bit;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->gmp_x00  = gmp_init(0x00);
        $this->gmp_x7f  = gmp_init(0x7f);
        $this->gmp_x80  = gmp_init(0x80);
        $this->gmp_xff  = gmp_init(0xff);
        $this->gmp_x100 = gmp_init(0x100);
        $this->is32Bit  = BigEndian::is32Bit();
    }

    /**
     * {@inheritdoc}
     */
    public function encodeVarint($varint)
    {
        $bytes = [];
        $value = $this->is32Bit
           ? gmp_and($varint, '0x0ffffffffffffffff')
           : sprintf('%u', $varint);

        while (gmp_cmp($value, $this->gmp_x00) > 0) {
            $bytes[] = gmp_intval(gmp_and($value, $this->gmp_x7f)) | 0x80;
            $value   = gmp_div_q($value, $this->gmp_x80);
        }

        return $bytes;
    }

    /**
     * {@inheritdoc}
     */
    public function encodeSFixed64($sFixed64)
    {
        $value = $this->is32Bit
            ? gmp_and($sFixed64, '0x0ffffffffffffffff')
            : gmp_init(sprintf('%u', $sFixed64));

        $bytes = '';

        for ($i = 0; $i < 8; ++$i) {
            $bytes .= chr(gmp_intval(gmp_and($value, $this->gmp_xff)));
            $value  = gmp_div_q($value, $this->gmp_x100);
        }

        return $bytes;
    }
}
