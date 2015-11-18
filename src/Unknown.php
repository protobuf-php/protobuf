<?php

namespace Protobuf;

/**
 * Unknown value
 *
 * @author Iván Montes <drslump@pollinimini.net>
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class Unknown
{
    /**
     * @var integer
     */
    public $tag;

    /**
     * @var integer
     */
    public $type;

    /**
     * @var mixed
     */
    public $value;

    /**
     * @param integer $tag
     * @param integer $type
     * @param mixed   $value
     */
    public function __construct($tag = 0, $type = null, $value = null)
    {
        $this->tag   = $tag;
        $this->type  = $type;
        $this->value = $value;
    }
}
