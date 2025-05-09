<?php

declare(strict_types=1);

namespace Cgrate\Php\Enums;

/**
 * This class defines all possible response codes that can be returned
 * by the Cgrate api endpoints, along with their human-readable descriptions.
 *
 * Note: This is implemented as a class with constants for PHP 7.4 compatibility
 * instead of using PHP 8.1 enums.
 */
class ResponseCode
{
    public const UNKNOWN = -1;
    public const SUCCESS = 0;
    public const INSUFFICIENT_BALANCE = 1;
    public const GENERAL_ERROR = 6;
    public const INVALID_MSISDN = 7;
    public const PROCESS_DELAY = 8;
    public const BALANCE_UPDATE_FAILED = 9;
    public const BALANCE_RETRIEVAL_FAILED = 10;
    public const ACCOUNT_ACTIVE = 31;
    public const ACCOUNT_INACTIVE = 32;
    public const ACCOUNT_SUSPENDED = 33;
    public const ACCOUNT_CLOSED = 34;
    public const PASSWORD_TRIES_EXCEEDED = 35;
    public const INCORRECT_PASSWORD = 36;
    public const ACCOUNT_NOT_EXIST = 37;
    public const INSUFFICIENT_STOCK = 51;
    public const INVALID_VOUCHER_REQUEST = 52;
    public const INVALID_RECHARGE = 53;
    public const INVALID_RECHARGE_DENOMINATION = 54;
    public const VOUCHER_PROVIDER_CONTENT_FAILED = 55;
    public const INVALID_VOUCHER_PROVIDER = 56;
    public const INVALID_DISTRIBUTION_CHANNEL = 101;
    public const USSD_TRANSACTION_NOT_AVAILABLE = 102;
    public const TRANSACTION_REFERENCE_NOT_UNIQUE = 104;
    public const INVALID_TRANSACTION_REFERENCE = 106;
    public const RECONCILIATION_FAILED = 151;
    public const NO_RECONCILIATION_FOUND = 152;
    public const RECONCILIATION_FLAG_INCONSISTENT = 153;
    public const ERROR_RECEIVING_RECONCILIATION_TOTAL = 154;
    public const NO_TRANSACTIONS_FOUND = 213;

    protected static array $descriptions = [
        self::UNKNOWN => 'Unknown response code',
        self::SUCCESS => 'Success',
        self::INSUFFICIENT_BALANCE => 'Insufficient balance',
        self::GENERAL_ERROR => 'General error',
        self::INVALID_MSISDN => 'Invalid MSISDN',
        self::PROCESS_DELAY => 'Process delay',
        self::BALANCE_UPDATE_FAILED => 'Balance update failed',
        self::BALANCE_RETRIEVAL_FAILED => 'Balance retrieval failed',
        self::ACCOUNT_ACTIVE => 'Account is active',
        self::ACCOUNT_INACTIVE => 'Account in inactive',
        self::ACCOUNT_SUSPENDED => 'Account is suspended',
        self::ACCOUNT_CLOSED => 'Account is closed',
        self::PASSWORD_TRIES_EXCEEDED => 'Password tries exceeded',
        self::INCORRECT_PASSWORD => 'Incorrect password',
        self::ACCOUNT_NOT_EXIST => 'Account does not exist',
        self::INSUFFICIENT_STOCK => 'Insufficient stock',
        self::INVALID_VOUCHER_REQUEST => 'Invalid voucher request',
        self::INVALID_RECHARGE => 'Invalid recharge',
        self::INVALID_RECHARGE_DENOMINATION => 'Invalid recharge denomination',
        self::VOUCHER_PROVIDER_CONTENT_FAILED => 'Voucher Provider content failed',
        self::INVALID_VOUCHER_PROVIDER => 'Invalid Voucher Provider',
        self::INVALID_DISTRIBUTION_CHANNEL => 'Invalid distribution channel',
        self::USSD_TRANSACTION_NOT_AVAILABLE => 'USSD transaction not available',
        self::TRANSACTION_REFERENCE_NOT_UNIQUE => 'Transaction reference not unique, please verify transaction through query transaction',
        self::INVALID_TRANSACTION_REFERENCE => 'Invalid transaction reference',
        self::RECONCILIATION_FAILED => 'Reconciliation failed',
        self::NO_RECONCILIATION_FOUND => 'No reconciliation found',
        self::RECONCILIATION_FLAG_INCONSISTENT => 'Reconciliation flag not consistent with count',
        self::ERROR_RECEIVING_RECONCILIATION_TOTAL => 'Error receiving reconciliation total',
        self::NO_TRANSACTIONS_FOUND => 'No transactions found',
    ];

    /**
     * Get a normalized response code from a string or integer value.
     *
     * @param  int $code The response code value
     * @return int Normalized response code
     */
    public static function fromValue(int $code): int
    {
        return isset(self::$descriptions[$code]) ? $code : self::UNKNOWN;
    }

    /**
     * Get the description for a response code value.
     *
     * @param  int $code The response code value
     * @return string The human-readable description
     */
    public static function getDescription(int $code): string
    {
        return self::$descriptions[$code] ?? self::$descriptions[self::UNKNOWN];
    }

    /**
     * Check if a response code matches the success code.
     *
     * @param  int $code The code to check
     * @return bool True if the code indicates success
     */
    public static function isSuccess(int $code): bool
    {
        return $code === self::SUCCESS;
    }
}
