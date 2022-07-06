<?php

$others = [
    [
        'role' => '*',
        'controller' => 'Pages',
        'action' => 'display',
    ],
    [
        'role' => '*',
        'plugin' => 'DebugKit',
        'controller' => '*',
        'action' => '*',
        'bypassAuth' => true,
    ],
];