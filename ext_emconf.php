<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "rkw_form"
 *
 * Auto generated by Extension Builder 2019-08-26
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'RkwForm',
    'description' => 'Provides forms. With FormFramework support from Typo3 version 8.7.',
    'category' => 'plugin',
    'author' => 'Maximilian Fäßler, Steffen Kroggel',
    'author_email' => 'faesslerweb@web.de, developer@steffenkroggel.de',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '8.7.7',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-8.7.99',
            'rkw_registration' => '8.7.20-8.7.99',
            'rkw_mailer' => '8.7.20-8.7.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
