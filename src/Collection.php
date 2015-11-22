<?php

namespace Protobuf;

use IteratorAggregate;
use Countable;

/**
 * Collection.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface Collection extends IteratorAggregate, Countable
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
     * Removes the element at the specified index from the collection.
     *
     * @param mixed $key
     *
     * @return mixed The removed element
     */
    public function remove($key);

    /**
     * Gets all values of the collection.
     *
     * @return array
     */
    public function getValues();
}
