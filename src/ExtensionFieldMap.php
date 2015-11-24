<?php

namespace Protobuf;

use InvalidArgumentException;
use OutOfBoundsException;

/**
 * A table of known extensions values
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ExtensionFieldMap extends BaseCollection
{
    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var string
     */
    private $extendee;

    /**
     * @param string $extendee
     */
    public function __construct($extendee = null)
    {
        $this->extendee = trim($extendee, '\\');
    }

    /**
     * Adds an element to set.
     *
     * @param \Protobuf\Extension $extension
     * @param mxied               $value
     */
    public function put(Extension $extension, $value)
    {
        $extendee = trim($extension->getExtendee(), '\\');
        $name     = $extension->getName();

        if ($extendee !== $this->extendee) {
            throw new InvalidArgumentException(sprintf(
                'Extension extendee must be a %s, %s given',
                $this->extendee,
                $extendee
            ));
        }

        $this->values[$name] = [$extension, $value];
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return empty($this->values);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        if ( ! $key instanceof Extension) {
            throw new InvalidArgumentException(sprintf(
                'Argument 1 passed to %s must a instanceof \Protobuf\Extension, %s given',
                __METHOD__,
                is_object($key) ? get_class($key) : gettype($key)
            ));
        }

        if ( ! isset($this->values[$key->getName()])) {
            throw new OutOfBoundsException(sprintf("Undefined index '%s'", $key->getName()));
        }

        return $this->values[$key->getName()][1];
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        $name = $extension->getName();

        if ( ! isset($this->values[$name])) {
            throw new OutOfBoundsException("Undefined index '$name'");
        }

        $removed = $this->values[$name];

        unset($this->values[$name]);

        return $removed[1];
    }

    /**
     * {@inheritdoc}
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
     * @param \Protobuf\ComputeSizeContext $context
     *
     * @return integer
     */
    public function serializedSize(ComputeSizeContext $context)
    {
        $size = 0;

        foreach ($this->values as $entry) {
            $extension = $entry[0];
            $value     = $entry[1];

            $size += $extension->serializedSize($context, $value);
        }

        return $size;
    }

    /**
     * @param \Protobuf\WriteContext $context
     */
    public function writeTo(WriteContext $context)
    {
        foreach ($this->values as $entry) {
            $extension = $entry[0];
            $value     = $entry[1];

            $extension->writeTo($context, $value);
        }
    }
}
