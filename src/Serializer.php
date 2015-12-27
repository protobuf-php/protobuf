<?php

namespace Protobuf;

/**
 * Protocol buffer serializer
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface Serializer
{
    /**
     * Serializes the given message.
     *
     * @param \Protobuf\Message $message
     *
     * @return \Protobuf\Stream
     */
    public function serialize(Message $message);

    /**
     * Deserializes the given data to the specified message.
     *
     * @param string                           $class
     * @param \Protobuf\Stream|resource|string $stream
     *
     * @return \Protobuf\Message
     */
    public function unserialize($class, $stream);
}
