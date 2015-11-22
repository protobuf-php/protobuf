<?php

namespace Protobuf;

use InvalidArgumentException;
use OutOfBoundsException;
use ArrayIterator;
use ArrayAccess;

/**
 * Base collection
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class BaseCollection implements Collection, ArrayAccess
{
    /**
     * @var array
     */
    protected $values;

    /**
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->values);
    }

    /**
     * @param mixed $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        if ( ! isset($this->values[$key])) {
            throw new OutOfBoundsException("Undefined index '$key'");
        }

        $removed = $this->values[$key];

        unset($this->values[$key]);

        return $removed;
    }

    /**
     * Gets all values of the collection.
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->values);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->values);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if ( ! isset($this->values[$offset])) {
            throw new OutOfBoundsException("Undefined index '$offset'");
        }

        return $this->values[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if ($value === null) {
            throw new InvalidArgumentException("Invalid NULL element");
        }

        if ($offset !== null) {
            $this->values[$offset] = $value;

            return;
        }

        $this->values[] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        if ( ! isset($this->values[$offset])) {
            throw new OutOfBoundsException("Undefined index '$offset'");
        }

        unset($this->values[$offset]);
    }
}
