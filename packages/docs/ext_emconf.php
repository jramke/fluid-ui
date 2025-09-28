<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Docs',
    'description' => '',
    'category' => 'templates',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
        ],
        'conflicts' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'FluidPrimitives\\Docs\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Joost Ramke',
    'author_email' => 'hey@joostramke.com',
    'author_company' => 'jramke',
    'version' => '1.0.0',
];
