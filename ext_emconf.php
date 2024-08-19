<?php

// @phpstan-ignore-next-line
$EM_CONF[$_EXTKEY] = [
    'title' => '+Pluswerk: Cache Automation',
    'description' => 'The extension clear caches with some magic automated in the right moment',
    'category' => 'be',
    'state' => 'stable',
    'author' => 'Markus Hölzle',
    'author_email' => 'markus.hoelzle@pluswerk.ag',
    'author_company' => '+Pluswerk AG',
    'constraints' => [
        'depends' => [
            'typo3' => '11.4.0-12.99.99',
        ],
        'conflicts' => [],
    ],
];
