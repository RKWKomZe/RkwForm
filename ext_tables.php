<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
    {

        //=================================================================
        // Register Plugins
        //=================================================================
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'RKW.RkwForm',
            'StandardForm',
            'RKW Form: Standard Formular'
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'RKW.RkwForm',
            'BstForm',
            'RKW Form: BST Formular'
        );

        //=================================================================
        // Add tables
        //=================================================================
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
            'tx_rkwform_domain_model_standardform'
        );

        //=================================================================
        // Add TypoScript
        //=================================================================
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
            $extKey,
            'Configuration/TypoScript',
            'RKW Form'
        );

        //=================================================================
        // Add Flexform
        //=================================================================

        // add here all form plugins, which should use this standard flexform
        $pluginList = ['StandardForm', 'BstForm'];

        foreach ($pluginList as $plugin) {
            $extensionName = strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($extKey));
            $pluginName = strtolower($plugin);
            $pluginSignature = $extensionName.'_'.$pluginName;

            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,pages';
            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
                $pluginSignature,
                'FILE:EXT:'. $extKey . '/Configuration/FlexForms/Standard.xml'
            );
        }

    },
    $_EXTKEY
);




