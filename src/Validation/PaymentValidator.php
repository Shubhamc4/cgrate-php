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

        if (! self::isValidMobileNumber($payment->customerMobile)) {
            $errors['customerMobile'][] = 'Invalid mobile number. Please ensure it starts with 260 and is a valid Zamtel, MTN, or Airtel number.';
        }

        if (! self::isValidReference($payment->paymentReference)) {
            $errors['paymentReference'][] = 'Payment reference contains invalid characters. Only alphanumeric characters and hyphens are allowed.';
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
        return (bool) preg_match("/^(260)[79][567]\d{7}$/", $mobileNumber);
    }

    /**
     * Check if the payment reference is valid.
     * 
     * @param  string  $reference  The payment reference to validate
     * @return  bool  True if reference is valid
     */
    public static function isValidReference(string $reference): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9\-]+$/', $reference) && strlen($reference) > 0;
    }
}
