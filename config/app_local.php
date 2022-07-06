<?php
/*
 * Local configuration file to provide any overrides to your app.php configuration.
 * Copy and save this file as app_local.php and make changes as required.
 * Note: It is not recommended to commit files with credentials such as app_local.php
 * into source code version control.
 */
return [
    /*
     * Debug Level:
     *
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

    /*
     * Security and encryption configuration
     *
     * - salt - A random string used in security hashing methods.
     *   The salt value is also used as the encryption key.
     *   You should treat it as extremely sensitive data.
     */
    'Security' => [
        'salt' => env('SECURITY_SALT', '82791aca277dd77041e1a654a2b922bbde99c74032231336f34b369d1f4222f7'),
    ],

    /*
     * Connection information used by the ORM to connect
     * to your application's datastores.
     *
     * See app.php for more configuration options.
     */
    'Datasources' => [
        'default' => [
            'host' => env('MYSQL_ADDON_HOST'),
            'port' => env('MYSQL_ADDON_PORT'),
            'username' => env('MYSQL_ADDON_USER'),
            'password' => env('MYSQL_ADDON_PASSWORD'),
            'database' => env('MYSQL_ADDON_DB'),
            'url' => env('MYSQL_ADDON_URI', null),
        ],

        /*
         * The test connection is used during the test suite.
         */
        'test' => [
            'host' => env('MYSQL_ADDON_HOST'),
            'username' => env('MYSQL_ADDON_USER'),
            'password' => env('MYSQL_ADDON_PASSWORD'),
            'database' => env('MYSQL_ADDON_DB_TEST'),
        ],
    ],

    /*
     * Email configuration.
     *
     * Host and credential configuration in case you are using SmtpTransport
     *
     * See app.php for more configuration options.
     */
    'EmailTransport' => [
        'default' => [
            'host' => 'localhost',
            'port' => 25,
            'username' => null,
            'password' => null,
            'client' => null,
            'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),
        ],
    ],
];
