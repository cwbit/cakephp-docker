<?php

$customUsers = [
    [
        'role' => ['admin','supervisor','operator'],
        'controller' => 'CustomUsers',
        'action' => ['postLoginRedirect'],
        'allowed' => true,
    ],
    [
        'role' => 'admin',
        'controller' => 'CustomUsers',
        'action' => '*',
        'allowed' => true,
    ],
];
