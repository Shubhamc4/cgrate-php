<?php

declare(strict_types=1);

namespace Cgrate\Php\Config;

class CgrateConfig
{
    public const ENDPOINT = 'https://543.cgrate.co.zm/Konik/KonikWs?wsdl';
    public const TEST_ENDPOINT = 'http://test.543.cgrate.co.zm:55555/Konik/KonikWs?wsdl';

    public static function getDefaultOptions(bool $test_mode): array
    {
        return [
            'soap_version' => SOAP_1_1,
            'connection_timeout' => 30,
            'keep_alive' => false,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => $test_mode,
            'exceptions' => $test_mode,
        ];
    }

    /**
     * Create a new config array by providing credentials
     *
     * @param string $username  Cgrate account username
     * @param string $password  Cgrate account password
     * @param bool   $test_mode Set to false for production environment
     *
     * @return array{username: string, password: string,
     * endpoint: string, test_endpoint: string, options: array}
     * Cgrate configuration
     */
    public static function create(string $username, string $password, bool $test_mode = false): array
    {
        return [
            'username' => $username,
            'password' => $password,
            'endpoint' => self::ENDPOINT,
            'test_endpoint' => self::TEST_ENDPOINT,
            'test_mode' => $test_mode,
            'options' => self::getDefaultOptions($test_mode)
        ];
    }
}
