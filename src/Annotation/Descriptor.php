<?php

namespace Protobuf\Annotation;

/**
 * Annotation that describes a proto message
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 *
 * @Annotation
 * @Target({"CLASS"})
 */
class Descriptor
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $package;

    /**
     * @var array
     */
    public $fields;
}
