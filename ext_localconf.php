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
        //  set default value depending on condition
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/form']['beforeRendering']['1695737224'] = \RKW\RkwForm\Domain\Model\Renderable\SetDynamicDefaultValue::class;


        //=================================================================
        // Add XClasses for extending existing classes
        //=================================================================
        // for TYPO3 12+
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\Madj2k\FeRegister\Domain\Model\FrontendUser::class] = [
            'className' => \RKW\RkwForm\Domain\Model\FrontendUser::class
        ];

        // for TYPO3 9.5 - 11.5 only, not required for TYPO3 12
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
            ->registerImplementation(
                \Madj2k\FeRegister\Domain\Model\FrontendUser::class,
                \RKW\RkwForm\Domain\Model\FrontendUser::class
            );

        // for TYPO3 12+
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\RKW\RkwForm\Domain\Model\BackendUser::class] = [
            'className' => \Madj2k\FeRegister\Domain\Model\BackendUser::class
        ];

        // for TYPO3 9.5 - 11.5 only, not required for TYPO3 12
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
            ->registerImplementation(
                \Madj2k\FeRegister\Domain\Model\BackendUser::class,
                \RKW\RkwForm\Domain\Model\BackendUser::class
            );


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
   'rkw_form'
);
