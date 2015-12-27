<?php

namespace Protobuf;

use ReflectionClass;
use BadMethodCallException;
use UnexpectedValueException;

/**
 * Base Enum
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class Enum
{
    /**
     * Enum value
     *
     * @var integer
     */
    protected $value;

    /**
     * Enum name
     *
     * @var string
     */
    protected $name;

    /**
     * @param string  $name
     * @param integer $value
     */
    public function __construct($name, $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }
}
