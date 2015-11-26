<?php

namespace Protobuf\Extension;

/**
 * A table of known extensions indexed by extendee and field number
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ExtensionRegistry
{
    /**
     * @var array
     */
    protected $extensions = [];

    /**
     * Remove all registered extensions
     */
    public function clear()
    {
        $this->extensions = [];
    }

    /**
     * Adds an element to the registry.
     *
     * @param \Protobuf\Extension\ExtensionField $extension
     */
    public function add(ExtensionField $extension)
    {
        $extendee = trim($extension->getExtendee(), '\\');
        $number   = $extension->getTag();

        if ( ! isset($this->extensions[$extendee])) {
            $this->extensions[$extendee] = [];
        }

        $this->extensions[$extendee][$number] = $extension;
    }

    /**
     * Find an extension by containing field number
     *
     * @param string  $className
     * @param integer $number
     *
     * @return \Protobuf\Extension\ExtensionField|null
     */
    public function findByNumber($className, $number)
    {
        $extendee = trim($className, '\\');

        if ( ! isset($this->extensions[$extendee][$number])) {
            return null;
        }

        return $this->extensions[$extendee][$number];
    }
}
