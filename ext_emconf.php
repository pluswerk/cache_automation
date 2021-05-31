<?php

// @phpstan-ignore-next-line
$EM_CONF[$_EXTKEY] = [
    'title' => '+Pluswerk: Cache Automation',
    'description' => 'The extension clear caches with some magic automated in the right moment',
    'category' => 'be',
    'dependencies' => '',
    'conflicts' => '',
    'priority' => '',
    'module' => '',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'author' => 'Markus Hölzle',
    'author_email' => 'markus.hoelzle@pluswerk.ag',
    'author_company' => '+Pluswerk AG',
    'version' => '1.2.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-10.4.99',
        ],
        'conflicts' => [],
    ],
];
