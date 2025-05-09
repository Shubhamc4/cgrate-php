<?php

declare(strict_types=1);

namespace Cgrate\Php\Exceptions;

use SoapFault;

class ConnectionException extends CgrateException
{
    /**
     * Create a new connection exception from a SoapFault.
     *
     * @param  SoapFault $fault   The SoapFault instance
     * @param  string    $context Additional context about the operation being performed
     * @return self
     */
    public static function fromSoapFault(SoapFault $fault, string $context): self
    {
        return new self(
            $context . ': ' . $fault->getMessage(),
            null,
            0,
            $fault
        );
    }
}
