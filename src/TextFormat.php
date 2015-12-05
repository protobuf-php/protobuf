<?php

namespace Protobuf;

use Traversable;
use Protobuf\Enum;
use Protobuf\Stream;
use ReflectionClass;
use ReflectionProperty;
use Protobuf\MessageInterface;

/**
 * This serializes to text format
 *
 * @author Iván Montes <drslump@pollinimini.net>
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class TextFormat
{
    /**
     * @param \Protobuf\Message $message
     * @param integer           $level
     *
     * @return \Protobuf\Stream
     */
    public function encodeMessage(Message $message, $level = 0)
    {
        $reflect    = new ReflectionClass($message);
        $properties = $reflect->getProperties(ReflectionProperty::IS_PROTECTED);
        $indent     = str_repeat('  ', $level);
        $stream     = Stream::create();

        foreach ($properties as $property) {

            $property->setAccessible(true);

            $name  = $property->getName();
            $value = $property->getValue($message);

            if ($value === null) {
                continue;
            }

            if ( ! is_array($value) && ! ($value instanceof Traversable)) {

                if ( ! $value instanceof Message) {
                    $item   = $this->encodeValue($value);
                    $buffer = $indent . $name . ': ' . $item . PHP_EOL;

                    $stream->write($buffer, strlen($buffer));

                    continue;
                }

                $innerStream  = $this->encodeMessage($value, $level + 1);
                $beginMessage = $indent . $name . ' {' . PHP_EOL;
                $endMessage   = $indent . '}' . PHP_EOL;

                $stream->write($beginMessage, strlen($beginMessage));
                $stream->writeStream($innerStream, $innerStream->getSize());
                $stream->write($endMessage, strlen($endMessage));

                continue;
            }

            foreach ($value as $val) {
                // Skip nullified repeated values
                if ($val == null) {
                    continue;
                }

                if ( ! $val instanceof Message) {
                    $item   = $this->encodeValue($val);
                    $buffer = $indent . $name . ': ' . $item . PHP_EOL;

                    $stream->write($buffer, strlen($buffer));

                    continue;
                }

                $innerStream  = $this->encodeMessage($val, $level + 1);
                $beginMessage = $indent . $name . ' {' . PHP_EOL;
                $endMessage   = $indent . '}' . PHP_EOL;

                $stream->write($beginMessage, strlen($beginMessage));
                $stream->writeStream($innerStream, $innerStream->getSize());
                $stream->write($endMessage, strlen($endMessage));
            }
        }

        $stream->seek(0);

        return $stream;
    }

    /**
     * @param scalar|array $value
     *
     * @return string
     */
    public function encodeValue($value)
    {
        if (is_bool($value)) {
            return (int) $value;
        }

        if ($value instanceof Enum) {
            return $value->name();
        }

        if ($value instanceof Stream) {
            return json_encode($value->__toString());
        }

        return json_encode($value);
    }
}
