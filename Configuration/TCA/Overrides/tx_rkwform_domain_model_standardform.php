<?php

$tempPagesColumns = array(

    'bst_number1' => array(
        'exclude' => 0,
        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.bst_number1',
        'config' => array(
            'type' => 'input',
            'size' => 5,
            'eval' => 'trim, required, num',
        ),
    ),
    'bst_number2' => array(
        'exclude' => 0,
        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.bst_number2',
        'config' => array(
            'type' => 'input',
            'size' => 5,
            'eval' => 'trim, required, num',
        ),
    ),
    'bst_number3' => array(
        'exclude' => 0,
        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.bst_number3',
        'config' => array(
            'type' => 'input',
            'size' => 5,
            'eval' => 'trim, required, num',
        ),
    ),

    'bst_agree' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.bst_agree',
        'config' => [
            'type' => 'check',
        ],
    ],

    'token' => [
        'exclude' => true,
        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.token',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim'
        ],
    ],

    'valid_until' => array(
        'exclude' => 0,
        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.valid_until',
        'config' => array(
            'type' => 'input',
            'size' => 11,
            'eval' => 'trim, required, num',
        ),
    ),

    'identifier' => [
        'exclude' => true,
        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.identifier',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim'
        ],
    ],

    'city' => [
        'exclude' => true,
        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.city',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim'
        ],
    ],

    'enabled' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.bst_agree',
        'config' => [
            'type' => 'check',
        ],
    ],

);
// Add TCA
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'tx_rkwform_domain_model_standardform',
    $tempPagesColumns
);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('tx_rkwform_domain_model_standardform', 'tx_rkwform_palette_bst', 'bst_number1,bst_number2,bst_number3,bst_agree');

// Add palette to new tab
$tempConfig = '
    --div--;LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.tabs.bst,
    --palette--;LLL:EXT:rkw_form/Resources/Private/Language/locallang_db.xlf:tx_rkwform_domain_model_standardform.palettes.bst;tx_rkwform_domain_model_standardform,
   ';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tx_rkwform_domain_model_standardform',
    $tempConfig
);
