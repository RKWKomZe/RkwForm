<?php
defined('TYPO3_MODE') || die('Access denied.');

///=================================================================
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
    'RKW Form: Bausachverständigentag Digital'
);
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'RKW.RkwForm',
    'DoubleOptinForm',
    'RKW Form: DoubleOptinForm'
);



//=================================================================
// Add Flexform
//=================================================================
$extKey = 'rkw_form';

$extensionName = strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($extKey));
$pluginName = strtolower('StandardForm');
$pluginSignature = $extensionName.'_'.$pluginName;
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:'. $extKey . '/Configuration/FlexForms/Standard.xml'
);

$extensionName = strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($extKey));
$pluginName = strtolower('BstForm');
$pluginSignature = $extensionName.'_'.$pluginName;
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:'. $extKey . '/Configuration/FlexForms/Standard.xml'
);

$extensionName = strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($extKey));
$pluginName = strtolower('DoubleOptinForm');
$pluginSignature = $extensionName.'_'.$pluginName;
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:'. $extKey . '/Configuration/FlexForms/Standard.xml'
);
