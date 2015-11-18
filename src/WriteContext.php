<?php

namespace Protobuf;

use Protobuf\Stream;
use Protobuf\Configuration;
use Protobuf\Binary\StreamWriter;

/**
 * Write context
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class WriteContext
{
    /**
     * @var \Protobuf\ComputeSizeContext
     */
    private $computeSizeContext;

    /**
     * @var \Protobuf\Binary\StreamWriter
     */
    private $writer;

    /**
     * @var \Protobuf\Stream
     */
    private $stream;

    /**
     * @var integer
     */
    private $length;

    /**
     * @param \Protobuf\Stream              $stream
     * @param \Protobuf\Binary\StreamWriter $writer
     * @param \Protobuf\ComputeSizeContext  $computeSizeContext
     */
    public function __construct(Stream $stream, StreamWriter $writer, ComputeSizeContext $computeSizeContext)
    {
        $this->stream             = $stream;
        $this->writer             = $writer;
        $this->computeSizeContext = $computeSizeContext;
    }

    /**
     * @return \Protobuf\Binary\StreamWriter
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * @return \Protobuf\Stream
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * @return \Protobuf\ComputeSizeContext
     */
    public function getComputeSizeContext()
    {
        return $this->computeSizeContext;
    }
}
