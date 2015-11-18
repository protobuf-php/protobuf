<?php

namespace Protobuf;

use Protobuf\Binary\SizeCalculator;

/**
 * Compute Size Context
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ComputeSizeContext
{
    /**
     * @var \Protobuf\Binary\SizeCalculator
     */
    private $calculator;

    /**
     * @param \Protobuf\Binary\SizeCalculator $calculator
     */
    public function __construct(SizeCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * @return \Protobuf\Binary\SizeCalculator
     */
    public function getSizeCalculator()
    {
        return $this->calculator;
    }
}
