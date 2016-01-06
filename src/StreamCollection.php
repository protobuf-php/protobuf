<?php

namespace Protobuf;

use InvalidArgumentException;
use ArrayObject;

/**
 * Protobuf Stream collection
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StreamCollection extends ArrayObject implements Collection
{
    /**
     * @param array<\Protobuf\Stream> $values
     */
    public function __construct(array $values = [])
    {
        array_walk($values, [$this, 'add']);
    }

    /**
     * Adds a \Protobuf\Stream to this collection
     *
     * @param \Protobuf\Stream $stream
     */
    public function add(Stream $stream)
    {
        parent::offsetSet(null, $stream);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if ( ! $value instanceof Stream) {
            throw new InvalidArgumentException(sprintf(
                'Argument 2 passed to %s must be a \Protobuf\Stream, %s given',
                __METHOD__,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        parent::offsetSet($offset, $value);
    }
}
