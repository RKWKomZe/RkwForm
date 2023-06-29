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

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'RKW.RkwForm',
            'GemCommunityForm',
            [
                'GemCommunityForm' => 'new, create, verify'
            ],
            // non-cacheable actions
            [
                'GemCommunityForm' => 'new, create, verify'
            ]
        );


        //=================================================================
        // Register SignalSlots
        //=================================================================
        /**
         * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher
         */
        $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);

        // @deprecated start
        $signalSlotDispatcher->connect(
            RKW\RkwForm\Controller\StandardFormController::class,
            \RKW\RkwForm\Controller\StandardFormController::SIGNAL_AFTER_REQUEST_CREATED_USER,
            RKW\RkwForm\Service\RkwMailService::class,
            'userMail'
        );

        $signalSlotDispatcher->connect(
            RKW\RkwForm\Controller\StandardFormController::class,
            \RKW\RkwForm\Controller\StandardFormController::SIGNAL_AFTER_REQUEST_CREATED_ADMIN,
            RKW\RkwForm\Service\RkwMailService::class,
            'adminMail'
        );
        // @deprecated end

        $signalSlotDispatcher->connect(
            RKW\RkwForm\Controller\AbstractFormController::class,
            \RKW\RkwForm\Controller\AbstractFormController::SIGNAL_AFTER_REQUEST_CREATED_USER,
            RKW\RkwForm\Service\RkwMailService::class,
            'userMail'
        );

        $signalSlotDispatcher->connect(
            RKW\RkwForm\Controller\AbstractFormController::class,
            \RKW\RkwForm\Controller\AbstractFormController::SIGNAL_AFTER_REQUEST_CREATED_ADMIN,
            RKW\RkwForm\Service\RkwMailService::class,
            'adminMail'
        );

        $signalSlotDispatcher->connect(
            'RKW\\RkwForm\\Controller\\GemCommunityFormController',
            \RKW\RkwForm\Controller\GemCommunityFormController::SIGNAL_AFTER_REQUEST_CREATED_USER,
            'RKW\\RkwForm\\Service\\RkwMailService',
            'verifyMail'
        );

        $signalSlotDispatcher->connect(
            'RKW\\RkwForm\\Controller\\GemCommunityFormController',
            \RKW\RkwForm\Controller\GemCommunityFormController::SIGNAL_AFTER_REQUEST_CREATED_ADMIN,
            'RKW\\RkwForm\\Service\\RkwMailService',
            'adminNotificationMail'
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
            \TYPO3\CMS\Core\Log\LogLevel::WARNING => array(
                // add a FileWriter
                'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => array(
                    // configuration for the writer
                    'logFile' => \TYPO3\CMS\Core\Core\Environment::getVarPath() .'/log/tx_rkwform.log'
                )
            ),
        );
    },
    $_EXTKEY
);
