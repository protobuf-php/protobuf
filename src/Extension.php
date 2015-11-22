<?php

namespace Protobuf;

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
    private $writer;

    /**
     * @var callback
     */
    private $reader;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $tag;

    /**
     * @param string   $name
     * @param integer  $tag
     * @param callback $reader
     * @param callback $writer
     */
    public function __construct($name, $tag, $reader, $writer)
    {
        $this->tag    = $tag;
        $this->name   = $name;
        $this->reader = $reader;
        $this->writer = $writer;
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
}
