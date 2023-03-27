<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
    {
        //=================================================================
        // Add TypoScript
        //=================================================================
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
            'rkw_form',
            'Configuration/TypoScript',
            'RKW Form'
        );

    },
    'rkw_form'
);
