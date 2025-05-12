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
     * @return  self
     */
    public static function withErrors(array $errors): self
    {
        $exception = new self('Validation of the submitted data failed.');
        $exception->errors = $errors;

        return $exception;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
