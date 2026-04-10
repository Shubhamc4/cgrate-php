<?php

declare(strict_types=1);

namespace CGrate\Php\Validation;

use CGrate\Php\Config\CGrateConfig;
use CGrate\Php\Exceptions\ValidationException;

/**
 * Validator for CGrate configuration.
 *
 * Validates configuration values to ensure all required settings
 * are properly configured before initializing the API client.
 */
final class ConfigValidator
{
    private static array $requiredKeys = [
        'username',
        'password',
        'endpoint',
        'testEndpoint',
    ];

    private static array $allowedHosts = [
        '543.cgrate.co.zm',
        'test.543.cgrate.co.zm',
    ];

    /**
     * Validate CGrate configuration.
     *
     * @param  array $config The configuration array to validate
     * @return  array{username:string,password:string,endpoint:string,testEndpoint:string,testMode:bool,options:array}
     * The validated and normalized configuration
     * @throws  \CGrate\Php\Exceptions\ValidationException  If configuration is invalid
     */
    public static function validate(array $config): array
    {
        $config = self::prepareForValidation($config);

        $errors = [];

        foreach (self::$requiredKeys as $key) {
            if (! isset($config[$key]) || empty($config[$key])) {
                $errors[$key] = "The '{$key}' configuration value is required";
            }
        }

        if (isset($config['endpoint']) && ! filter_var($config['endpoint'], FILTER_VALIDATE_URL)) {
            $errors['endpoint'] = 'The endpoint must be a valid URL';
        }

        if (isset($config['testEndpoint']) && ! filter_var($config['testEndpoint'], FILTER_VALIDATE_URL)) {
            $errors['testEndpoint'] = 'The test endpoint must be a valid URL';
        }

        $testMode = $config['testMode'] ?? false;

        if (! $testMode && isset($config['endpoint']) && parse_url($config['endpoint'], PHP_URL_SCHEME) !== 'https') {
            $errors['endpoint'] = 'The production endpoint must use HTTPS';
        }

        if (isset($config['endpoint']) && ! self::isAllowedHost($config['endpoint'])) {
            $errors['endpoint'] = 'The endpoint host is not an allowed CGrate domain';
        }

        if (isset($config['testEndpoint']) && ! self::isAllowedHost($config['testEndpoint'])) {
            $errors['testEndpoint'] = 'The test endpoint host is not an allowed CGrate domain';
        }

        if (! isset($config['testMode'])) {
            $config['testMode'] = false;
        }

        if (! isset($config['options']) || ! is_array($config['options'])) {
            $config['options'] = CGrateConfig::getDefaultOptions($config['testMode']);
        } else {
            $config['options'] = array_merge(CGrateConfig::getDefaultOptions($config['testMode']), $config['options']);
        }

        if (! empty($errors)) {
            throw ValidationException::withErrors($errors);
        }

        return $config;
    }

    /**
     * Trim and sanitize all string config values before validation.
     * Credentials are XML-escaped to prevent injection in SOAP headers.
     *
     * @param  array  $config  The raw configuration array
     * @return  array  The cleaned configuration array
     */
    private static function prepareForValidation(array $config): array
    {
        foreach ($config as $key => $value) {
            if (is_string($value)) {
                $config[$key] = trim($value);
            }
        }

        if (isset($config['username'])) {
            $config['username'] = htmlspecialchars($config['username'], ENT_XML1 | ENT_QUOTES, 'UTF-8');
        }

        if (isset($config['password'])) {
            $config['password'] = htmlspecialchars($config['password'], ENT_XML1 | ENT_QUOTES, 'UTF-8');
        }

        return $config;
    }

    /**
     * Check if the host of the given URL is in the CGrate allowed hosts list.
     *
     * @param  string  $url  The URL to check
     * @return  bool  True if the host is allowed
     */
    private static function isAllowedHost(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        foreach (self::$allowedHosts as $allowed) {
            if ($host === $allowed || str_ends_with($host, '.'.$allowed)) {
                return true;
            }
        }

        return false;
    }
}