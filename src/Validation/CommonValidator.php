<?php

declare(strict_types=1);

namespace CGrate\Php\Validation;

/**
 * Shared validation utilities for CGrate request data.
 *
 * Provides common validation methods that can be used by consumers
 * to pre-validate data before constructing DTOs.
 */
final class CommonValidator
{
    public const INVALID_MOBILE_NUMBER_MESSAGE = 'Invalid mobile number. Please ensure it starts with 260 and is a valid Zamtel, MTN, or Airtel number.';
    public const INVALID_REFERENCE_MESSAGE = 'Reference contains invalid characters or exceeds 50 characters. Only alphanumeric characters and hyphens are allowed.';

    /**
     * Check if a mobile number is valid for Zambia.
     * Accepts international format only (260XXXXXXXXX).
     *
     * @param  string  $mobileNumber  The mobile number to validate
     * @return  bool  True if mobile number is valid
     */
    public static function isValidMobileNumber(string $mobileNumber): bool
    {
        return (bool) preg_match("/^(260)(97|77|57|76|96|95|75)\d{7}$/", $mobileNumber);
    }

    /**
     * Check if a transaction or deposit reference is valid.
     * Only alphanumeric characters and hyphens are allowed, max 50 characters.
     *
     * @param  string  $reference  The reference to validate
     * @return  bool  True if reference is valid
     */
    public static function isValidReference(string $reference): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9\-]+$/', $reference)
            && strlen($reference) > 0
            && strlen($reference) <= 50;
    }
}
