<?php

declare(strict_types=1);

namespace CGrate\Php\Validation;

use CGrate\Php\DTOs\CashDepositRequestDTO;
use CGrate\Php\Exceptions\ValidationException;

final class CashDepositValidator
{
    /**
     * Validate a cash deposit request DTO.
     *
     * @param  \CGrate\Php\DTOs\CashDepositRequestDTO  $cashDeposit  The cash deposit request to validate
     * @return  bool  True if validation passes
     * @throws  ValidationException  If validation fails
     */
    public static function validate(CashDepositRequestDTO $cashDeposit): bool
    {
        $errors = [];

        if ($cashDeposit->transactionAmount <= 0) {
            $errors['transactionAmount'][] = 'The transaction amount must be greater than zero';
        }

        if (! self::isValidMobileNumber($cashDeposit->customerAccount)) {
            $errors['customerAccount'][] = 'Invalid mobile number. Please ensure it starts with 260 and is a valid Zamtel, MTN, or Airtel number.';
        }

        if (! self::isValidReference($cashDeposit->depositorReference)) {
            $errors['depositorReference'][] = 'Deposit reference contains invalid characters. Only alphanumeric characters and hyphens are allowed.';
        }

        if (! empty($errors)) {
            throw ValidationException::withErrors($errors);
        }

        return true;
    }

    /**
     * Check if the mobile number is valid for Zambia.
     * 
     * @param  string  $mobileNumber  The mobile number to validate
     * @return  bool  True if mobile number is valid
     */
    public static function isValidMobileNumber(string $mobileNumber): bool
    {
        return (bool) preg_match("/^(260|0)(97|77|57|76|96|95|75)\d{7}$/", $mobileNumber);
    }

    /**
     * Check if the deposit reference is valid.
     * 
     * @param  string  $reference  The deposit reference to validate
     * @return  bool  True if reference is valid
     */
    public static function isValidReference(string $reference): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9\-]+$/', $reference) && strlen($reference) > 0;
    }
}
