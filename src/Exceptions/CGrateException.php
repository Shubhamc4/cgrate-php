<?php

declare(strict_types=1);

namespace CGrate\Php\Exceptions;

use CGrate\Php\Enums\ResponseCode;
use Exception;
use Throwable;

/**
 * Base exception for all CGrate package exceptions.
 */
abstract class CGrateException extends Exception
{
    /**
     * The response code from the API, if available.
     */
    protected ?int $responseCode = null;

    /**
     * Create a new CGrate exception.
     *
     * @param  string  $message  The exception message
     * @param  int|null  $responseCode  The response code from the API
     * @param  int|null  $code  The exception code
     * @param  Throwable|null  $previous  The previous exception
     */
    public function __construct(
        string $message,
        ?int $responseCode = null,
        ?int $code = null,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            code: $code ?? $previous?->getCode() ?? 0,
            previous: $previous
        );

        $this->responseCode = $responseCode !== null ? (int) $responseCode : null;
    }

    public function getResponseCode(): ?int
    {
        return $this->responseCode ?? ResponseCode::UNKNOWN->value;
    }
}
