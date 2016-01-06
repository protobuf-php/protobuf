<?php

namespace Protobuf;

use InvalidArgumentException;
use ArrayObject;

/**
 * Scalar collection
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ScalarCollection extends ArrayObject implements Collection
{
    /**
     * @param array<scalar> $values
     */
    public function __construct(array $values = [])
    {
        array_walk($values, [$this, 'add']);
    }

    /**
     * Adds a value to this collection
     *
     * @param scalar $value
     */
    public function add($value)
    {
        if ( ! is_scalar($value)) {
            throw new InvalidArgumentException(sprintf(
                'Argument 1 passed to %s must be a scalar value, %s given',
                __METHOD__,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        parent::offsetSet(null, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if ( ! is_scalar($value)) {
            throw new InvalidArgumentException(sprintf(
                'Argument 2 passed to %s must be a scalar value, %s given',
                __METHOD__,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        parent::offsetSet($offset, $value);
    }
}
