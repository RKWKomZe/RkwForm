<?php
namespace RKW\RkwForm\Controller;

use \TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use \RKW\RkwForm\Domain\Model\StandardForm;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Messaging\AbstractMessage;
use \RKW\RkwBasics\Helper\Common;
use \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Class AbstractFormController
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class AbstractFormController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * Signal name for use in ext_localconf.php
     *
     * @const string
     */
    const SIGNAL_AFTER_REQUEST_CREATED_USER = 'afterRequestCreatedUser';

    /**
     * Signal name for use in ext_localconf.php
     *
     * @const string
     */
    const SIGNAL_AFTER_REQUEST_CREATED_ADMIN = 'afterRequestCreatedAdmin';

    /**
     * standardFormRepository
     *
     * @var \RKW\RkwForm\Domain\Repository\StandardFormRepository
     * @inject
     */
    protected $standardFormRepository = null;

    /**
     * FrontendUserRepository
     *
     * @var \RKW\RkwForm\Domain\Repository\FrontendUserRepository
     * @inject
     */
    protected $frontendUserRepository;

    /**
     * BackendUserRepository
     *
     * @var \RKW\RkwForm\Domain\Repository\BackendUserRepository
     * @inject
     */
    protected $backendUserRepository;



    /**
     * action initialize
     * @return void
     */
    public function initializeAction()
    {
        // workaround for a specific settings problem: Extbase does not longer distinguish between different plugin configurations
        // Second problem: If we would overwrite the whole settings array itself, the flexform settings would fly away. So let's merge
        // Hint: If a plugin has no specific settings, nothing further will happen. The standard settings would be used then
        $pluginSpecificSettings = $this->getPluginSettings();
        array_merge($this->settings, $pluginSpecificSettings);
    }



    /**
     * action new
     * @param \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $standardForm
     * @return void
     */
    public function newAction(AbstractEntity $standardForm = null)
    {
        $this->view->assign('standardForm', $standardForm);
    }




    /**
     * action createAbstract
     * (do not call this function directly. Add an "createAction" instead with specific Model param and specific validator)
     *
     * @param \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $standardForm
     * @param int $privacy
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException if the slot is not valid
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException if a slot return
     */
    public function createAbstractAction(AbstractEntity $standardForm, $privacy = 0)
    {

        if (!$privacy) {
            $this->addFlashMessage(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                    'registrationController.error.accept_privacy', 'rkw_registration'
                ),
                '',
                AbstractMessage::ERROR
            );
            $this->forward('new', null, null, array('standardForm' => $standardForm));
            //===
        }

        // give form to mailHandling function
        $this->mailHandling($standardForm);
        $this->standardFormRepository->add($standardForm);

    }


    /**
     * Remove ErrorFlashMessage
     *
     * @see \TYPO3\CMS\Extbase\Mvc\Controller\ActionController::getErrorFlashMessage()
     */
    protected function getErrorFlashMessage()
    {
        return false;
        //===
    }


    /**
     * mail handling
     * @param \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $formRequest
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException if the slot is not valid
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException if a slot return
     */
    protected function mailHandling(AbstractEntity $formRequest)
    {
        /** @var \RKW\RkwRegistration\Domain\Model\FrontendUser $frontendUser */
        $frontendUser = GeneralUtility::makeInstance('RKW\\RkwRegistration\\Domain\\Model\\FrontendUser');
        $frontendUser->setEmail($formRequest->getEmail());
        $frontendUser->setFirstName($formRequest->getFirstName());
        $frontendUser->setLastName($formRequest->getLastName());
        if (
            ($formRequest->getTitle())
            && ($formRequest->getTitle()->getIsIncludedInSalutation())
            && (! $formRequest->getTitle()->getIsTitleAfter())
        ) {
            $frontendUser->setTitle($formRequest->getTitle()->getName());
        }

        $frontendUser->setTxRkwregistrationLanguageKey($GLOBALS['TSFE']->config['config']['language'] ? $GLOBALS['TSFE']->config['config']['language'] : 'de');

        /*
        // currently we do not use real privacy-entries
        if ($this->settings['includeRkwRegistrationPrivacy']) {
            // add privacy info
            \RKW\RkwRegistration\Tools\Privacy::addPrivacyData($this->request, $frontendUser, $newSupportRequest, 'new support request');
        }
        */

        // send final confirmation mail to user
        $this->signalSlotDispatcher->dispatch(__CLASS__, self::SIGNAL_AFTER_REQUEST_CREATED_USER, array($frontendUser, $formRequest));

        // send information mail to admins
        $adminUidList = explode(',', $this->settings['mail']['backendUser']);
        $backendUsers = array();
        foreach ($adminUidList as $adminUid) {
            if ($adminUid) {
                $admin = $this->backendUserRepository->findByUid($adminUid);
                if ($admin) {
                    $backendUsers[] = $admin;
                }
            }
        }

        // fallback-handling
        if (
            (count($backendUsers) < 1)
            && ($backendUserFallback = intval($this->settings['mail']['fallbackBackendUser']))
        ) {
            $admin = $this->backendUserRepository->findByUid($backendUserFallback);
            if (
                ($admin)
                && ($admin->getEmail())
            ) {
                $backendUsers[] = $admin;
            }

        }

        $this->signalSlotDispatcher->dispatch(__CLASS__, self::SIGNAL_AFTER_REQUEST_CREATED_ADMIN, array($backendUsers, $formRequest));
    }



    /**
     * Returns plugin specific settings
     *
     * @param string $which Which type of settings will be loaded
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected function getPluginSettings($which = ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS)
    {
        $pluginName = $this->request->getPluginName();

        return Common::getTyposcriptConfiguration('Rkwform_'.$pluginName, $which);
        //===
    }
}
