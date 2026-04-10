<?php

declare(strict_types=1);

namespace CGrate\Php\Validation;

use CGrate\Php\DTOs\PaymentRequestDTO;
use CGrate\Php\Exceptions\ValidationException;

final class PaymentValidator
{
    /**
     * Validate a payment request DTO.
     *
     * @param  PaymentRequestDTO  $payment  The payment request to validate
     * @return  bool  True if validation passes
     * @throws  ValidationException  If validation fails
     */
    public static function validate(PaymentRequestDTO $payment): bool
    {
        $errors = [];

        if ($payment->transactionAmount <= 0) {
            $errors['transactionAmount'][] = 'The transaction amount must be greater than zero';
        }

        if (! CommonValidator::isValidMobileNumber($payment->customerMobile)) {
            $errors['customerMobile'][] = CommonValidator::INVALID_MOBILE_NUMBER_MESSAGE;
        }

        if (! CommonValidator::isValidReference($payment->paymentReference)) {
            $errors['paymentReference'][] = CommonValidator::INVALID_REFERENCE_MESSAGE;
        }

        if (! empty($errors)) {
            throw ValidationException::withErrors($errors);
        }

        return true;
    }
}
