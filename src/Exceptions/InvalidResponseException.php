<?php

declare(strict_types=1);

namespace CGrate\Php\Exceptions;

use CGrate\Php\Enums\ResponseCode;

final class InvalidResponseException extends CGrateException
{
    /**
     * Create a new invalid response exception from a response code.
     *
     * @param  ResponseCode  $responseCode  The response code from the API
     * @return  self  New invalid response exception instance
     */
    public static function fromResponseCode(ResponseCode $responseCode): self
    {
        return new self(
            message: "CGrate API returned an error: ".$responseCode->getDescription(),
            responseCode: $responseCode->value
        );
    }

    /**
     * Create a new invalid response exception for an unexpected response format.
     *
     * @param  string  $context  Additional context about the operation being performed
     * @return  self  New invalid response exception instance
     */
    public static function unexpectedFormat(string $context = ''): self
    {
        $message = $context ? "{$context}: " : '';
        $message .= 'Unexpected API response format';

        return new self(message: $message, code: 500);
    }
}
