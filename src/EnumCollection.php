<?php

namespace Protobuf;

use InvalidArgumentException;
use ArrayObject;

/**
 * Protobuf enum collection
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class EnumCollection extends ArrayObject implements Collection
{
    /**
     * @param array<\Protobuf\Enum> $values
     */
    public function __construct(array $values = [])
    {
        array_walk($values, [$this, 'add']);
    }

    /**
     * Adds a \Protobuf\Enum to this collection
     *
     * @param \Protobuf\Enum $enum
     */
    public function add(Enum $enum)
    {
        parent::offsetSet(null, $enum);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if ( ! $value instanceof Enum) {
            throw new InvalidArgumentException(sprintf(
                'Argument 2 passed to %s must be a \Protobuf\Enum, %s given',
                __METHOD__,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        parent::offsetSet($offset, $value);
    }
}
