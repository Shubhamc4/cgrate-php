<?php

declare(strict_types=1);

namespace CGrate\Php\Config;

final readonly class CGrateConfig
{
    public const ENDPOINT = 'https://543.cgrate.co.zm/Konik/KonikWs?wsdl';
    public const TEST_ENDPOINT = 'http://test.543.cgrate.co.zm:55555/Konik/KonikWs?wsdl';

    /**
     * Default Options for the cGrate client
     *  
     * @param  bool  $testMode  Set to false for production environment
     * @return  array{soap_version:int, connection_timeout:int, keep_alive:bool, 
     * cache_wsdl:int, exceptions:bool, trace:bool}
     */
    public static function getDefaultOptions(bool $testMode): array
    {
        return [
            'soap_version' => SOAP_1_1,
            'connection_timeout' => 30,
            'keep_alive' => false,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'exceptions' => true,
            'trace' => $testMode,
        ];
    }

    /**
     * Create a new config array by providing credentials
     *
     * @param  string  $username  CGrate account username
     * @param  string  $password  CGrate account password
     * @param  bool  $testMode  Set to false for production environment
     *
     * @return  array{username: string, password: string,
     * endpoint: string, testEndpoint: string, options: array}
     * CGrate configuration
     */
    public static function create(string $username, string $password, bool $testMode = false): array
    {
        return [
            'username' => $username,
            'password' => $password,
            'endpoint' => self::ENDPOINT,
            'testEndpoint' => self::TEST_ENDPOINT,
            'testMode' => $testMode,
            'options' => self::getDefaultOptions($testMode)
        ];
    }
}
