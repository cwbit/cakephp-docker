<?php
$baseDir = dirname(dirname(__FILE__));

return [
    'plugins' => [
        'AssetMix' => $baseDir . '/vendor/ishanvyas22/asset-mix/',
        'Authentication' => $baseDir . '/vendor/cakephp/authentication/',
        'Authorization' => $baseDir . '/vendor/cakephp/authorization/',
        'Bake' => $baseDir . '/vendor/cakephp/bake/',
        'BootstrapUI' => $baseDir . '/vendor/friendsofcake/bootstrap-ui/',
        'CakeDC/Auth' => $baseDir . '/vendor/cakedc/auth/',
        'CakeDC/Users' => $baseDir . '/vendor/cakedc/users/',
        'Cake/TwigView' => $baseDir . '/vendor/cakephp/twig-view/',
        'CakephpFixtureFactories' => $baseDir . '/vendor/vierge-noire/cakephp-fixture-factories/',
        'CakephpTestSuiteLight' => $baseDir . '/vendor/vierge-noire/cakephp-test-suite-light/',
        'DebugKit' => $baseDir . '/vendor/cakephp/debug_kit/',
        'Duplicatable' => $baseDir . '/vendor/riesenia/cakephp-duplicatable/',
        'Migrations' => $baseDir . '/vendor/cakephp/migrations/',
        'SoftDelete' => $baseDir . '/vendor/salines/cakephp4-soft-delete/',
    ],
];
