<?php

declare(strict_types=1);

namespace Cgrate\Php\Exceptions;

class ValidationException extends CgrateException
{
    protected array $errors = [];

    /**
     * Create a new validation exception with the given errors.
     *
     * @param  array $errors The validation errors
     * @return self
     */
    public static function withErrors(array $errors): self
    {
        $exception = new self('The given data failed validation.');
        $exception->errors = $errors;

        return $exception;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
