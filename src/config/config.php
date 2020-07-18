<?php
return [

    /**
     * Enabling/disabling cache using
     */
    'use_cache' => true,

    /**
     * Section configurations
     */
    'sections' => [

        // Section 'codifier_example'
        'codifier_example' => [
            // Config path to section data
            'data_path' => 'laragrad.codifier.codifier_example',
            // Translate base path to section translations
            'trans_base_path' => 'laragrad/codifier::example',
            // Handler class and method
            'handler' => \Laragrad\Codifier\Handlers\CodifierExampleHandler::class,
        ],

    ]
];