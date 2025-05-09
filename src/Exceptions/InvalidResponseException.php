<?php

declare(strict_types=1);

namespace Cgrate\Php\Exceptions;

use Cgrate\Php\Enums\ResponseCode;

class InvalidResponseException extends CgrateException
{
    /**
     * Create a new invalid response exception from a response code.
     *
     * @param  int $responseCode The response code from the API
     * @return self
     */
    public static function fromResponseCode(int $responseCode): self
    {
        return new self(
            "CGrate API returned an error: " . ResponseCode::getDescription($responseCode),
            $responseCode
        );
    }

    /**
     * Create a new invalid response exception for an unexpected response format.
     *
     * @param  string $context Additional context about the operation being performed
     * @return self
     */
    public static function unexpectedFormat(string $context = ''): self
    {
        $message = $context ? "{$context}: " : '';
        $message .= 'Unexpected API response format';

        return new self($message);
    }
}
