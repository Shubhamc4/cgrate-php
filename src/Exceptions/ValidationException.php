<?php

declare(strict_types=1);

namespace CGrate\Php\Exceptions;

final class ValidationException extends CGrateException
{
    protected array $errors = [];

    /**
     * Create a new validation exception with the given errors.
     *
     * @param  array  $errors  The validation errors
     * @return  self  New validation exception instance
     */
    public static function withErrors(array $errors): self
    {
        $exception = new self(message: 'The given data was invalid.', code: 422);
        $exception->errors = $errors;

        return $exception;
    }

    /**
     * Get the validation errors.
     * 
     * @return  array  The validation errors
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
