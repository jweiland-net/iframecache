<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'IFrame Cache',
    'description' => 'This extension loads the content of an iFrame and cached it. All links, images and sources will be overridden.',
    'category' => 'module',
    'author' => 'Stefan Froemken',
    'author_email' => 'projects@jweiland.net',
    'author_company' => 'jweiland.net',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.35-11.5.23',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
