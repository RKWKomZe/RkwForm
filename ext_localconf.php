<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
	{

        //=================================================================
        // Configure Plugin
        //=================================================================
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'RKW.RkwForm',
            'StandardForm',
            [
                'StandardForm' => 'new, create'
            ],
            // non-cacheable actions
            [
                'StandardForm' => 'new, create'
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'RKW.RkwForm',
            'BstForm',
            [
                'BstForm' => 'new, create'
            ],
            // non-cacheable actions
            [
                'BstForm' => 'new, create'
            ]
        );

        //=================================================================
        // Register SignalSlots
        //=================================================================
        /**
         * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher
         */
        $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');

        $signalSlotDispatcher->connect(
            'RKW\\RkwForm\\Controller\\StandardFormController',
            \RKW\RkwForm\Controller\StandardFormController::SIGNAL_AFTER_REQUEST_CREATED_USER,
            'RKW\\RkwForm\\Service\\RkwMailService',
            'userMail'
        );

        $signalSlotDispatcher->connect(
            'RKW\\RkwForm\\Controller\\StandardFormController',
            \RKW\RkwForm\Controller\StandardFormController::SIGNAL_AFTER_REQUEST_CREATED_ADMIN,
            'RKW\\RkwForm\\Service\\RkwMailService',
            'adminMail'
        );

    },
    $_EXTKEY
);
