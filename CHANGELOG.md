# Changelog

All notable changes to `cgrate-php` will be documented in this file.

## 2.0.3 - 2026-04-10

### Fixed

- `BalanceResponseDTO::toArray()` was returning the enum description instead of the raw API response message
- Wrong error message in `processCashDeposit()` catch block (was "reverse customer payment")
- Undefined offset warning in `getCustomerIssuerName()` when input does not match the regex
- `ValidationException` was incorrectly placed inside the `SoapFault` try/catch in `processCustomerPayment()` and `processCashDeposit()`; it now propagates directly to the caller
- `ConfigValidator` URL checks were accessing `$config['endpoint']` before confirming the key exists, causing PHP 8 warnings on missing keys
- Whitespace-only strings (e.g. `"   "`) were passing `ConfigValidator` required field checks
- `CGrateException::getResponseCode()` declared `?int` but never returned null; corrected to `int`
- `validateResponse()` only checked for property existence, not that `->return` is non-null
- `getAvailableCashDepositIssuers()` had an unreachable `?? []` fallback after a validated response

### Security

- XML injection: credentials are now XML-escaped (`htmlspecialchars` with `ENT_XML1 | ENT_QUOTES`) in `ConfigValidator::prepareForValidation()` before being used in the SOAP security header
- Production endpoint now enforced to use HTTPS; HTTP is only permitted in test mode
- SSRF mitigation: endpoint and testEndpoint URLs are validated against a domain allowlist (`543.cgrate.co.zm`, `test.543.cgrate.co.zm`)
- `SoapFault` messages are no longer forwarded directly into `ConnectionException` to prevent leaking internal infrastructure details
- WSDL caching changed from `WSDL_CACHE_NONE` to `WSDL_CACHE_DISK` in production mode to reduce WSDL re-fetch exposure

### Changed

- Mobile number validation (`260XXXXXXXXX` format) and reference validation are now centralised in a new `CommonValidator` class; both validators delegate to it
- Transaction and deposit references now have a maximum length of 50 characters enforced in validation
- `ConfigValidator` is now `final`, consistent with other validators
- `ConfigValidator` trims all string config values as part of `prepareForValidation()`, and returns the cleaned config
- `CGrateService` now uses a private `callSoap()` helper to eliminate repeated try/catch + response validation boilerplate across all SOAP methods
- `getAvailableCashDepositIssuers()` now validates that the returned array is non-empty and contains only non-empty strings; return type narrowed to `string[]`
- All response DTOs (`BalanceResponseDTO`, `PaymentResponseDTO`, `CashDepositResponseDTO`) now use null-safe fallbacks (`?? 0`, `?? ''`) in `fromResponse()` for `responseCode` and `responseMessage`

## 2.0.2 - 2025-07-16

- Added getAvailableCashDepositIssuers method to the list of all the valid cash deposit issuer
- Added processCashDeposit method to process cash deposit
- Added queryCustomerPayment method to query the customer payment status
- Removed invalid queryTransactionStatus and reverseCustomerPayment method

## 2.0.1 - 2025-05-12

- Fixed namespace inconsistency in ResponseCode enum (Cgrate → CGrate)
- Corrected getEndpoint() method to check testMode flag instead of testEndpoint
- Enhanced validation in ConfigValidator to properly handle testMode
- Added proper null checks for balance value in displayBalance()
- Fixed parameter validation to better handle missing values
- Improved exception classes with better typing and detailed descriptions

## 2.0.0 - 2025-05-12

- Upgrade package to support php 8.2 and above

### Changed

- Converted ResponseCode class to a proper PHP 8.2 Enum (BackedEnum)
- Upgraded all DTOs to use PHP 8.2 readonly classes with public properties
- Renamed namespace from `Cgrate\Php` to `CGrate\Php` for consistency
- Made class inheritance more explicit with final classes where appropriate
- Added proper property promotion in class constructors
- Updated parameter naming for consistency across the codebase
- Enhanced error messages for better debugging
- Improved method signatures with more specific types

### Fixed

- Fixed incorrect endpoint resolution in getEndpoint() method
- Fixed PHP 8.2 compatibility issues throughout the codebase
- Standardized exception handling with more specific error messages

## 1.0.0 - 2025-05-09

### Added

- Initial release
- Support for CGrate payment service integration for core php
- Core functionality:
  - Get account balance
  - Process customer payments
  - Query transaction status
  - Reverse payments
- Comprehensive exception handling
- Data Transfer Objects for convenient API interaction
- Validation for payment requests
- WS-Security authentication for SOAP API
- Detailed documentation and usage examples
