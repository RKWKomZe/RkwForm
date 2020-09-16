<?php
defined('TYPO3_MODE') || die('Access denied.');

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

