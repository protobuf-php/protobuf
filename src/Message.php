<?php

namespace Protobuf;

use Protobuf\ComputeSizeContext;
use Protobuf\Configuration;
use Protobuf\WriteContext;
use Protobuf\ReadContext;

/**
 * Base interface implemented by Protocol Message objects
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface Message
{
    /**
     * Creates message from the given stream.
     *
     * @param \Protobuf\Stream|resource|string $stream
     * @param \Protobuf\Configuration          $configuration
     *
     * @return \Protobuf\Message
     */
    public static function fromStream($stream, Configuration $configuration = null);

    /**
     * Serializes the message and returns a stream containing its bytes.
     *
     * @param \Protobuf\Configuration $configuration
     *
     * @return \Protobuf\Stream
     */
    public function toStream(Configuration $configuration = null);

    /**
     * Compute the number of bytes that would be needed to encode the message
     *
     * @param \Protobuf\ComputeSizeContext $context
     *
     * @return integer
     */
    public function serializedSize(ComputeSizeContext $context);

    /**
     * Serializes the message and returns a stream containing its bytes.
     *
     * @param \Protobuf\ReadContext $context
     */
    public function readFrom(ReadContext $context);

    /**
     * Encodes and writes the message
     *
     * @param \Protobuf\ReadContext $context
     *
     * @return \Protobuf\Stream
     */
    public function writeTo(WriteContext $context);

    /**
     * Merge $context into the message being built.
     *
     * @param \Protobuf\Message $message
     */
    public function merge(Message $message);

    /**
     * Obtain the list of unknown fields in this message.
     *
     * @return \Protobuf\UnknownFieldSet
     */
    public function unknownFieldSet();

    /**
     * Obtain the map of extensions in this message.
     *
     * @return \Protobuf\Extension\ExtensionFieldMap
     */
    public function extensions();

    /**
     * Reset all fields back to the initial values.
     */
    public function clear();
}
