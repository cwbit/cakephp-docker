<?php

return [
    'color_labels' => [
        'orange' => __('Orange'),
        'red' => __('Rouge'),
    ],
    'key_code_labels_admin' => [
        'Aucun' => null,
        'cc_red' => __('CC (rouge)'),
        'sc_blue_stars' => __('SC {star} (bleu)', ['star' => '★']),
        'sc_red' => __('SC (rouge)'),
        'sc' => __('SC'),
    ],
    'key_code_labels_front' => [
        'Aucun' => null,
        'cc_red' => __('CC'),
        'sc_blue_stars' => __('SC {star}', ['star' => '★']),
        'sc_red' => __('SC'),
        'sc' => __('SC'),
    ],

    'unit_labels' => [
        'milimeter' => __('mm'),
        'centimeter' => __('cm'),
        'meter' => __('m'),
        'celsius' => __('C°'),
        'fahrenheit' => __('F°'),
    ],
    'is_conform' => [
        'ok' => 'OK',
        'not_ok' => 'Non OK'
    ],
    'is_conform_with_na' => [
        'ok' => 'OK',
        'not_ok' => 'Non OK',
        'not_applicable' => 'Non applicable'
    ],
    'is_valid' => [
        true => 'OK',
        false => 'Non OK',
    ],
    'role_users' => [
        'admin' => __('Admin'),
        'supervisor' => __('Superviseur'),
        'operator' => __('Opérateur'),
    ],
    'is_active_question' => [
        false => 'Question activée',
        true => 'Question désactivée',
    ],
    'is_active_category' => [
        false => 'activée',
        true => 'désactivée',
    ]
];
