<?php
return [
    'use_cache' => true,
    'sections' => [
        'codifier_example' => [
            'data_path' => 'laragrad.codifier.codifier_example',
            'trans_base_path' => 'laragrad/codifier::example',
            'handler' => [
                \Laragrad\Codifier\Handlers\CodifierExampleHandler::class,
                'load'
            ]
        ]
    ]
];