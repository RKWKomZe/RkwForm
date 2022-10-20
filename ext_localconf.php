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
        // Register CommandController
        //=================================================================
   //     $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'RKW\\RkwForm\\Controller\\CommandController';


        //=================================================================
        // Register SignalSlots
        //=================================================================
        /**
         * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher
         */
        $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');

        // @deprecated start
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
        // @deprecated end

        $signalSlotDispatcher->connect(
            'RKW\\RkwForm\\Controller\\AbstractFormController',
            \RKW\RkwForm\Controller\AbstractFormController::SIGNAL_AFTER_REQUEST_CREATED_USER,
            'RKW\\RkwForm\\Service\\RkwMailService',
            'userMail'
        );

        $signalSlotDispatcher->connect(
            'RKW\\RkwForm\\Controller\\AbstractFormController',
            \RKW\RkwForm\Controller\AbstractFormController::SIGNAL_AFTER_REQUEST_CREATED_ADMIN,
            'RKW\\RkwForm\\Service\\RkwMailService',
            'adminMail'
        );

        //=================================================================
        // Hooks
        //=================================================================
        //  set token
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/form']['afterSubmit']['1'] = \RKW\RkwForm\Domain\Model\Renderable\SetTokenAndExpiration::class;
        //  set current base url
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/form']['afterSubmit']['2'] = \RKW\RkwForm\Domain\Model\Renderable\SetBaseUrl::class;
        //  get post parameter
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/form']['afterBuildingFinished']['3'] = \RKW\RkwForm\Domain\Model\Renderable\GetPostParameter::class;

        //=================================================================
        // Register Logger
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['RKW']['RkwForm']['writerConfiguration'] = array(

            // configuration for WARNING severity, including all
            // levels with higher severity (ERROR, CRITICAL, EMERGENCY)
            \TYPO3\CMS\Core\Log\LogLevel::DEBUG => array(
                // add a FileWriter
                'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => array(
                    // configuration for the writer
                    'logFile' => 'typo3temp/var/logs/tx_rkwform.log'
                )
            ),
        );

    },
    $_EXTKEY
);
