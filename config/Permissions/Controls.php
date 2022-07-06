<?php

$controls = [
    [
        'role' => 'admin',
        'controller' => 'Controls',
        'action' => '*',
        'allowed' => true,
    ],
    [
        'role' => '*',
        'controller' => 'Controls',
        'action' => ['loginOperator', 'add'],
        'bypassAuth' => true,
    ],
    [
        'role' => 'supervisor',
        'controller' => 'Controls',
        'action' => ['add'],
        'allowed' => false,
    ],
    [
        'role' => 'supervisor',
        'controller' => 'Controls',
        'action' => ['index', 'view'],
        'allowed' => true,
    ],
];
