<?php

namespace Protobuf;

use ArrayObject;

/**
 * Used to keep track of fields which were seen when Unknown value parsing a message
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class UnknownFieldSet extends ArrayObject implements Collection
{
    /**
     * Adds an element to set.
     *
     * @param \Protobuf\Unknown $unknown
     */
    public function add(Unknown $unknown)
    {
        $this->offsetSet($unknown->tag, $unknown);
    }
}
