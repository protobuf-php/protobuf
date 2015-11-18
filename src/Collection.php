<?php

namespace Protobuf;

use IteratorAggregate;
use ArrayAccess;
use Countable;

/**
 * Collection.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface Collection extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @return boolean
     */
    public function isEmpty();

    /**
     * @param mixed $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Gets all values of the collection.
     *
     * @return array
     */
    public function getValues();
}
