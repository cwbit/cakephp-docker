<?php

$checklists = [
    [
        'role' => 'admin',
        'controller' => 'Checklists',
        'action' => ['add', 'index', 'view', 'edit', 'delete', 'modifiedChecklists', 'viewModifiedChecklist', 'duplicate', 'validate'],
        'allowed' => true,
    ],
    [
        'role' => 'supervisor',
        'controller' => 'Checklists',
        'action' => ['index', 'view', 'edit', 'duplicate'],
        'allowed' => true,
    ],
];
