<?php

namespace Protobuf;

use RuntimeException;
use InvalidArgumentException;

/**
 * PHP stream implementation
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class Stream
{
    /**
     * @var resource
     */
    private $stream;

    /**
     * @var integer
     */
    private $size;

    /**
     * @param resource $stream
     * @param integer  $size
     *
     * @throws \InvalidArgumentException if the stream is not a stream resource
     */
    public function __construct($stream, $size = null)
    {
        if ( ! is_resource($stream)) {
            throw new InvalidArgumentException('Stream must be a resource');
        }

        $this->size   = $size;
        $this->stream = $stream;
    }

    /**
     * Closes the stream when the destructed
     */
    public function __destruct()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }

        $this->stream = null;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getContents();
    }

    /**
     * Returns the remaining contents of the stream as a string.
     *
     * @return string
     */
    public function getContents()
    {
        if ( ! $this->stream) {
            return '';
        }

        $this->seek(0);

        return stream_get_contents($this->stream);
    }

    /**
     * Get the size of the stream
     *
     * @return int|null Returns the size in bytes if known
     *
     * @throws \InvalidArgumentException If cannot find out the stream size
     */
    public function getSize()
    {
        if ($this->size !== null) {
            return $this->size;
        }

        if ( ! $this->stream) {
            return null;
        }

        $stats = fstat($this->stream);

        if (isset($stats['size'])) {
            return $this->size = $stats['size'];
        }

        throw new RuntimeException('Unknown stream size');
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        return feof($this->stream);
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int
     *
     * @throws \RuntimeException If cannot find out the stream position
     */
    public function tell()
    {
        $position = ftell($this->stream);

        if ($position === false) {
            throw new RuntimeException('Unable to get stream position');
        }

        return $position;
    }

    /**
     * Seek to a position in the stream
     *
     * @param int $offset
     * @param int $whence
     *
     * @throws \RuntimeException If cannot find out the stream position
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (fseek($this->stream, $offset, $whence) !== 0) {
            throw new RuntimeException('Unable to seek stream position to ' . $offset);
        }
    }

    /**
     * Read data from the stream
     *
     * @param int $length
     *
     * @return string
     */
    public function read($length)
    {
        if ($length < 1) {
            return '';
        }

        $buffer = fread($this->stream, $length);

        if ($buffer === false) {
            throw new RuntimeException('Failed to read ' . $length . ' bytes');
        }

        return $buffer;
    }

    /**
     * Read stream
     *
     * @param int $length
     *
     * @return \Protobuf\Stream
     *
     * @throws \RuntimeException
     */
    public function readStream($length)
    {
        $stream  = self::fromString();
        $target  = $stream->stream;
        $source  = $this->stream;

        if ($length < 1) {
            return $stream;
        }

        $written = stream_copy_to_stream($source, $target, $length);

        if ($written !== $length) {
            throw new RuntimeException('Failed to read stream with ' . $length . ' bytes');
        }

        $stream->seek(0);

        return $stream;
    }

    /**
     * Write data to the stream
     *
     * @param string $bytes
     * @param int    $length
     *
     * @return int
     *
     * @throws \RuntimeException
     */
    public function write($bytes, $length)
    {
        $written = fwrite($this->stream, $bytes, $length);

        if ($written !== $length) {
            throw new RuntimeException('Failed to write '.$length.' bytes');
        }

        $this->size = null;

        return $written;
    }

    /**
     * Write stream
     *
     * @param \Protobuf\Stream $stream
     * @param int              $length
     *
     * @return int
     *
     * @throws \RuntimeException
     */
    public function writeStream(Stream $stream, $length)
    {
        $target  = $this->stream;
        $source  = $stream->stream;
        $written = stream_copy_to_stream($source, $target);

        if ($written !== $length) {
            throw new RuntimeException('Failed to write stream with ' . $length . ' bytes');
        }

        $this->size = null;

        return $written;
    }

    /**
     * Wrap the input resource in a stream object.
     *
     * @param \Protobuf\Stream|resource|string $resource
     * @param integer                          $size
     *
     * @return \Protobuf\Stream
     *
     * @throws \InvalidArgumentException if the $resource arg is not valid.
     */
    public static function wrap($resource = '', $size = null)
    {
        if ($resource instanceof Stream) {
            return $resource;
        }

        $type = gettype($resource);

        if ($type == 'string') {
            return self::fromString($resource, $size);
        }

        if ($type == 'resource') {
            return new self($resource, $size);
        }

        throw new InvalidArgumentException('Invalid resource type: ' . $type);
    }

    /**
     * Create a new stream.
     *
     * @return \Protobuf\Stream
     */
    public static function create()
    {
        return new self(fopen('php://temp', 'r+'));
    }

    /**
     * Create a new stream from a string.
     *
     * @param string  $resource
     * @param integer $size
     *
     * @return \Protobuf\Stream
     */
    public static function fromString($resource = '', $size = null)
    {
        $stream = fopen('php://temp', 'r+');

        if ($resource !== '') {
            fwrite($stream, $resource);
            fseek($stream, 0);
        }

        return new self($stream, $size);
    }
}
