<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
	{

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'RKW.RkwForm',
            'Standard',
            [
                'Standard' => 'new, create'
            ],
            // non-cacheable actions
            [
                'Standard' => 'new, create'
            ]
        );

        /**
         * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher
         */
        $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');

        $signalSlotDispatcher->connect(
            'RKW\\RkwForm\\Controller\\StandardController',
            \RKW\RkwForm\Controller\StandardController::SIGNAL_AFTER_REQUEST_CREATED_USER,
            'RKW\\RkwForm\\Service\\RkwMailService',
            'userMail'
        );

        $signalSlotDispatcher->connect(
            'RKW\\RkwForm\\Controller\\StandardController',
            \RKW\RkwForm\Controller\StandardController::SIGNAL_AFTER_REQUEST_CREATED_ADMIN,
            'RKW\\RkwForm\\Service\\RkwMailService',
            'adminMail'
        );

    },
    $_EXTKEY
);
