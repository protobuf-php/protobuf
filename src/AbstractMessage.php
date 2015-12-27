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
     * {@inheritdoc}
     */
    public function __toString()
    {
        $format = new TextFormat();
        $stream = $format->encodeMessage($this);

        return $stream->__toString();
    }
}
