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

        if (! CommonValidator::isValidMobileNumber($cashDeposit->customerAccount)) {
            $errors['customerAccount'][] = CommonValidator::INVALID_MOBILE_NUMBER_MESSAGE;
        }

        if (! CommonValidator::isValidReference($cashDeposit->depositorReference)) {
            $errors['depositorReference'][] = CommonValidator::INVALID_REFERENCE_MESSAGE;
        }

        if (! empty($errors)) {
            throw ValidationException::withErrors($errors);
        }

        return true;
    }
}
