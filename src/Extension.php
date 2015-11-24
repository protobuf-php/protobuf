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
class Extension
{
    /**
     * @var callback
     */
    private $sizeCalculator;

    /**
     * @var callback
     */
    private $writer;

    /**
     * @var callback
     */
    private $reader;

    /**
     * @var string
     */
    private $extendee;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $tag;

    /**
     * @param string   $extendee
     * @param string   $name
     * @param integer  $tag
     * @param callback $reader
     * @param callback $writer
     * @param callback $sizeCalculator
     */
    public function __construct($extendee, $name, $tag, $reader, $writer, $sizeCalculator)
    {
        $this->tag            = $tag;
        $this->name           = $name;
        $this->reader         = $reader;
        $this->writer         = $writer;
        $this->extendee       = $extendee;
        $this->sizeCalculator = $sizeCalculator;
    }

    /**
     * @return string
     */
    public function getExtendee()
    {
        return $this->extendee;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return integer
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param \Protobuf\ComputeSizeContext $context
     * @param mixed                        $value
     *
     * @return integer
     */
    public function serializedSize(ComputeSizeContext $context, $value)
    {
        return call_user_func($this->sizeCalculator, $context, $value);
    }

    /**
     * @param \Protobuf\WriteContext $context
     * @param mixed                  $value
     */
    public function writeTo(WriteContext $context, $value)
    {
        call_user_func($this->writer, $context, $value);
    }

    /**
     * @param \Protobuf\ReadContext $context
     * @param integer               $wire
     *
     * @return mixed
     */
    public function readFrom(ReadContext $context, $wire)
    {
        return call_user_func($this->reader, $context, $wire);
    }
}
