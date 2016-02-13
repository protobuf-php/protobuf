<?php

namespace Protobuf\Extension;

use InvalidArgumentException;
use OutOfBoundsException;
use SplObjectStorage;

use Protobuf\Message;
use Protobuf\Collection;
use Protobuf\WriteContext;
use Protobuf\ComputeSizeContext;

/**
 * A table of known extensions values
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ExtensionFieldMap extends SplObjectStorage implements Collection
{
    /**
     * @var string
     */
    protected $extendee;

    /**
     * @param string $extendee
     */
    public function __construct($extendee = null)
    {
        $this->extendee = trim($extendee, '\\');
    }

    /**
     * @param \Protobuf\Extension\ExtensionField $extension
     * @param mixed                              $value
     */
    public function add(ExtensionField $extension, $value)
    {
        if ( ! $value instanceof Message) {
            $this->put($extension, $value);

            return;
        }

        $className = get_class($value);
        $existing  = isset($this[$extension])
            ? $this[$extension]
            : null;

        if ($existing instanceof $className) {
            $value->merge($existing);
        }

        $this->put($extension, $value);
    }

    /**
     * @param \Protobuf\Extension\ExtensionField $extension
     * @param mixed                              $value
     */
    public function put(ExtensionField $extension, $value)
    {
        $extendee = trim($extension->getExtendee(), '\\');

        if ($extendee !== $this->extendee) {
            throw new InvalidArgumentException(sprintf(
                'Invalid extendee, %s is expected but %s given',
                $this->extendee,
                $extendee
            ));
        }

        $this->attach($extension, $value);
    }

    /**
     * @param \Protobuf\Extension\ExtensionField $key
     *
     * @return mixed
     */
    public function get(ExtensionField $key)
    {
        return $this->offsetGet($key);
    }

    /**
     * @param \Protobuf\ComputeSizeContext $context
     *
     * @return integer
     */
    public function serializedSize(ComputeSizeContext $context)
    {
        $size = 0;

        for ($this->rewind(); $this->valid(); $this->next()) {
            $extension = $this->current();
            $value     = $this->getInfo();
            $size     += $extension->serializedSize($context, $value);
        }

        return $size;
    }

    /**
     * @param \Protobuf\WriteContext $context
     */
    public function writeTo(WriteContext $context)
    {
        for ($this->rewind(); $this->valid(); $this->next()) {
            $extension = $this->current();
            $value     = $this->getInfo();

            $extension->writeTo($context, $value);
        }
    }
}
