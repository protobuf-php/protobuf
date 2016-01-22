<?php

namespace Protobuf;

use Protobuf\TextFormat;

/**
 * Abstract message class
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class AbstractMessage implements Message
{
    /**
     * Message constructor
     *
     * @param \Protobuf\Stream|resource|string $stream
     * @param \Protobuf\Configuration          $configuration
     */
    public function __construct($stream = null, \Protobuf\Configuration $configuration = null)
    {
        if ($stream === null) {
            return;
        }

        $config  = $configuration ?: \Protobuf\Configuration::getInstance();
        $context = $config->createReadContext($stream);

        $this->readFrom($context);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $format = new TextFormat();
        $stream = $format->encodeMessage($this);

        return $stream->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public static function __set_state(array $values)
    {
        return static::fromArray($values);
    }
}
