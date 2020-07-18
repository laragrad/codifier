<?php

/**
 * There is list of abstract statuses as exapmle of codifier data
 */
return [
    1 => [
        'scode' => 'new',
        'next' => [
            2
        ],
        'desc' => 'laragrad/codifier::example.1.desc'
    ],
    2 => [
        'scode' => 'in_process',
        'next' => [
            1,
            3
        ],
        'desc' => 'laragrad/codifier::example.2.desc'
    ],
    3 => [
        'scode' => 'finished',
        'next' => null,
        'desc' => 'laragrad/codifier::example.3.desc'
    ]
];