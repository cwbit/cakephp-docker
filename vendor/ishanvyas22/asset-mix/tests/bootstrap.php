<?php
declare(strict_types=1);

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;

require_once __DIR__ . '/../vendor/autoload.php';

// Path constants to a few helpful things.
define('ROOT', dirname(__DIR__));
define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'vendor' . DS . 'cakephp' . DS . 'cakephp');
define('CORE_PATH', ROOT . DS . 'vendor' . DS . 'cakephp' . DS . 'cakephp' . DS);
define('CAKE', CORE_PATH . 'src' . DS);
define('TESTS', ROOT . DS . 'tests');
define('TEST_APP_DIR', ROOT . DS . 'tests' . DS . 'test_app' . DS);
define('APP_DIR', 'src');
define('APP', ROOT . DS . 'tests' . DS . 'test_app' . DS . APP_DIR . DS);
define('COMPARE_PATH', ROOT . DS . 'tests' . DS . 'test_files' . DS);
define('WEBROOT_DIR', 'webroot');
define('TMP', sys_get_temp_dir() . DS);
define('CONFIG', TEST_APP_DIR . 'config' . DS);
define('WWW_ROOT', TEST_APP_DIR . 'webroot' . DS);
define('CACHE', TMP);
define('LOGS', TMP);

require_once CORE_PATH . 'config/bootstrap.php';

date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');

Configure::write('debug', true);
Configure::write('App', [
    'namespace' => 'TestApp',
    'encoding' => 'UTF-8',
    'base' => false,
    'baseUrl' => false,
    'dir' => 'src',
    'webroot' => 'webroot',
    'wwwRoot' => WWW_ROOT,
    'fullBaseUrl' => 'http://localhost',
    'imageBaseUrl' => 'img/',
    'jsBaseUrl' => 'js/',
    'cssBaseUrl' => 'css/',
    'paths' => [
        'plugins' => [TEST_APP_DIR . 'plugins' . DS],
        'templates' => [TEST_APP_DIR . 'src' . DS . 'Template' . DS],
    ],
]);

Cache::setConfig([
    '_cake_core_' => [
        'engine' => 'File',
        'prefix' => 'cake_core_',
        'serialize' => true,
    ],
    '_cake_model_' => [
        'engine' => 'File',
        'prefix' => 'cake_model_',
        'serialize' => true,
    ],
    'default' => [
        'engine' => 'File',
        'prefix' => 'default_',
        'serialize' => true,
    ],
]);

require ROOT . DS . 'config' . DS . 'bootstrap.php';

// Ensure default test connection is defined
if (!getenv('db_dsn')) {
    putenv('db_dsn=sqlite://127.0.0.1/' . TMP . 'debug_kit_test.sqlite');
}
$config = [
    'url' => getenv('db_dsn'),
    'timezone' => 'UTC',
];
ConnectionManager::setConfig('test', $config);

Plugin::getCollection()->add(new \AssetMix\Plugin());
