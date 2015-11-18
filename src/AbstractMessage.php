<?php

namespace Protobuf;

use Protobuf\TextFormat;
use Protobuf\Configuration;

/**
 * Abstract message class
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class AbstractMessage implements Message
{
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $format = new TextFormat(Configuration::getInstance());
        $stream = $format->encodeMessage($this);

        return (string) $stream;
    }
}
