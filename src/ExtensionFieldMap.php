<?php

namespace Protobuf;

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
    protected $values;

    /**
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    /**
     * Adds an element to set.
     *
     * @param \Protobuf\Extension $extension
     * @param mxied               $value
     */
    public function put(Extension $extension, $value)
    {
        $this->values[$extension->getName()] = [$extension, $value];
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
        return $this->values[$extension->getName()][1];
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
