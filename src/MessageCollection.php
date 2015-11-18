<?php

namespace Protobuf;

use InvalidArgumentException;

/**
 * Message collection
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class MessageCollection extends BaseCollection
{
    /**
     * Adds a message to this collection
     *
     * @param \Protobuf\Message $message
     */
    public function add(Message $message)
    {
        $this->values[] = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if ( ! $value instanceof Message) {
            throw new InvalidArgumentException(sprintf(
                'Argument 2 passed to %s must implement interface \Protobuf\Message, %s given',
                __METHOD__,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        parent::offsetSet($offset, $value);
    }
}
