<?php

declare(strict_types=1);

namespace CGrate\Php\Exceptions;

use SoapFault;

final class ConnectionException extends CGrateException
{
    /**
     * Create a new connection exception from a SoapFault.
     *
     * @param  SoapFault  $fault  The SoapFault instance
     * @param  string  $context  Additional context about the operation being performed
     * @return  self  New connection exception instance
     */
    public static function fromSoapFault(SoapFault $fault, string $context): self
    {
        return new self(
            message: $context.': '.$fault->getMessage(),
            responseCode: null,
            code: 500,
            previous: $fault
        );
    }
}
