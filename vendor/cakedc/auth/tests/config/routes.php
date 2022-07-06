<?php

use Cake\Routing\Router;

Router::defaultRouteClass(\Cake\Routing\Route\DashedRoute::class);

Router::scope('/', function (\Cake\Routing\RouteBuilder $routes) {
    $routes->setExtensions(['other']);
    $routes->connect('/my-test', [
        'plugin' => 'CakeDC/Users',
        'controller' => 'Users',
        'action' => 'myTest',
    ]);
    $routes->connect('/test-named', [
        'plugin' => 'CakeDC/Users',
        'controller' => 'Users',
        'action' => 'myTest',
    ], [
        '_name' => 'testNamed',
    ]);
    $routes->connect('/tests/tests/test', [
        'plugin' => 'Tests',
        'controller' => 'Tests',
        'action' => 'test',
    ]);
    $routes->connect('/tests/tests/one', [
        'plugin' => 'Tests',
        'controller' => 'Tests',
        'action' => 'one',
    ]);
    $routes->connect('/tests/tests/three', [
        'plugin' => 'Tests',
        'controller' => 'Tests',
        'action' => 'three',
    ]);
    $routes->connect('/tests/tests/any', [
        'plugin' => 'Tests',
        'controller' => 'Tests',
        'action' => 'any',
    ]);
    $routes->connect('/tests/test-tests/test-action', [
        'plugin' => 'Tests',
        'controller' => 'TestTests',
        'action' => 'testAction',
    ]);
    $routes->connect('/tests2/test-tests/test-action', [
        'plugin' => 'tests',
        'controller' => 'test-tests',
        'action' => 'test-action',
    ]);
    $routes->connect('/any/any/any', [
        'plugin' => 'Any',
        'controller' => 'Any',
        'action' => 'any',
    ]);
    $routes->connect('/any/tests/test', [
        'plugin' => 'Any',
        'controller' => 'Tests',
        'action' => 'test',
    ]);
    $routes->connect('/something/something/test', [
        'plugin' => 'Something',
        'controller' => 'Something',
        'action' => 'test',
    ]);
    $routes->connect('/something/something/something', [
        'plugin' => 'Something',
        'controller' => 'Something',
        'action' => 'something',
    ]);
    $routes->connect('/csv/tests/one', [
        'prefix' => 'csv',
        'controller' => 'Tests',
        'action' => 'one',
    ]);
    $routes->connect('/ord/tests/test', [
        'plugin' => 'Ord',
        'controller' => 'Tests',
        'action' => 'test',
    ]);

    $routes->fallbacks(\Cake\Routing\Route\DashedRoute::class);

    $routes->setExtensions(['csv']);
    $routes->connect('/tests/one', [
        'controller' => 'Tests',
        'action' => 'one',
    ]);
});

Router::scope('/admin', ['prefix' => 'admin'], function (\Cake\Routing\RouteBuilder $routes) {
    $routes->setExtensions(['other', 'csv']);
    $routes->connect('/tests/one', [
        'controller' => 'Tests',
        'action' => 'one',
    ]);
});
