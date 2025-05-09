<?php

declare(strict_types=1);

namespace Cgrate\Php\Exceptions;

use Exception;
use Throwable;

class CgrateException extends Exception
{
    protected ?int $responseCode = null;

    /**
     * Create a new CGrate exception.
     *
     * @param string         $message      The exception message
     * @param int|null       $responseCode The response code from the API
     * @param int            $code         The exception code
     * @param Throwable|null $previous     The previous exception
     */
    public function __construct(
        string $message,
        ?int $responseCode = null,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->responseCode = $responseCode;
    }

    /**
     * Create a new connection exception.
     *
     * @param  string         $message  The exception message
     * @param  Throwable|null $previous The previous exception
     * @return self
     */
    public static function fromMessage(string $message, ?Throwable $previous = null): self
    {
        return new self(
            $message . ': ' . $previous->getMessage(),
            null,
            0,
            $previous
        );
    }

    public function getResponseCode(): ?int
    {
        return $this->responseCode;
    }
}
