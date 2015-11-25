<?php

namespace Protobuf;

use Protobuf\ReadContext;
use Protobuf\WriteContext;
use Protobuf\ComputeSizeContext;

/**
 * Protobuf extension field
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface Extension
{
    /**
     * @return string
     */
    public function getExtendee();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return integer
     */
    public function getTag();

    /**
     * @param \Protobuf\ComputeSizeContext $context
     * @param mixed                        $value
     *
     * @return integer
     */
    public function serializedSize(ComputeSizeContext $context, $value);

    /**
     * @param \Protobuf\WriteContext $context
     * @param mixed                  $value
     */
    public function writeTo(WriteContext $context, $value);

    /**
     * @param \Protobuf\ReadContext $context
     * @param integer               $wire
     *
     * @return mixed
     */
    public function readFrom(ReadContext $context, $wire);
}
