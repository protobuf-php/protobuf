<?php

namespace Protobuf\Binary\Platform;

use RuntimeException;

/**
 * Platform factory
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class PlatformFactory
{
    /**
     * @var \Protobuf\Binary\Platform\NegativeEncoder
     */
    private $negativeEncoder;

    /**
     * Return a NegativeEncoder.
     *
     * @return \Protobuf\Binary\Platform\NegativeEncoder
     */
    public function getNegativeEncoder()
    {
        if ($this->negativeEncoder !== null) {
            return $this->negativeEncoder;
        }

        if ($this->isExtensionLoaded('gmp')) {
            return $this->negativeEncoder = new GmpNegativeEncoder();
        }

        if ($this->isExtensionLoaded('bcmath') && ! $this->is32Bit()) {
            return $this->negativeEncoder = new BcNegativeEncoder();
        }

        return $this->negativeEncoder = new InvalidNegativeEncoder();
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function isExtensionLoaded($name)
    {
        return extension_loaded($name);
    }

    /**
     * @return boolean
     */
    public function is32Bit()
    {
        return BigEndian::is32Bit();
    }
}
