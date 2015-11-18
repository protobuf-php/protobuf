<?php

namespace Protobuf\Annotation;

/**
 * Annotation that describes a proto field
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 *
 * @Annotation
 * @Target({"ANNOTATION","PROPERTY"})
 */
class Field
{
    /**
     * @var integer
     */
    public $tag;

    /**
     * @var string
     */
    public $name;

    /**
     * @var integer
     */
    public $type;

    /**
     * @var integer
     */
    public $label;

    /**
     * @var boolean
     */
    public $pack;

    /**
     * @var mixed
     */
    public $default;

    /**
     * @var string
     */
    public $reference;
}
