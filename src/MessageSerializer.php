<?php

namespace Protobuf;

/**
 * Default protocol buffers serializer implementation
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class MessageSerializer implements Serializer
{
    /**
     * @var \Protobuf\Configuration
     */
    private $config;

    /**
     * @param \Protobuf\Configuration $config
     */
    public function __construct(Configuration $config = null)
    {
        $this->config = $config ?: Configuration::getInstance();
    }

    /**
     * @return \Protobuf\Configuration
     */
    public function getConfiguration()
    {
        return $this->config;
    }

    /**
     * Serializes the given message.
     *
     * @param \Protobuf\Message $message
     *
     * @return \Protobuf\Stream
     */
    public function serialize(Message $message)
    {
        return $message->toStream($this->config);
    }

    /**
     * Deserializes the given data to the specified message.
     *
     * @param string                           $class
     * @param \Protobuf\Stream|resource|string $stream
     *
     * @return \Protobuf\Message
     */
    public function unserialize($class, $stream)
    {
        return new $class($stream, $this->config);
    }
}
