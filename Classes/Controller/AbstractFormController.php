<?php
namespace RKW\RkwForm\Controller;
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

use RKW\RkwFeecalculator\Domain\Repository\CalculatorRepository;
use RKW\RkwForm\Domain\Repository\BackendUserRepository;
use RKW\RkwForm\Domain\Repository\FrontendUserRepository;
use RKW\RkwForm\Domain\Repository\StandardFormRepository;
use Madj2k\FeRegister\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use Madj2k\CoreExtended\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Class AbstractFormController
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
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
     * @var \RKW\RkwForm\Domain\Repository\StandardFormRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ?StandardFormRepository $standardFormRepository = null;


    /**
     * @var \RKW\RkwForm\Domain\Repository\FrontendUserRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ?FrontendUserRepository $frontendUserRepository = null;


    /**
     * @var \RKW\RkwForm\Domain\Repository\BackendUserRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ?BackendUserRepository $backendUserRepository = null;


    /**
     * @var \RKW\RkwForm\Domain\Repository\StandardFormRepository
     */
    public function injectStandardFormRepository(StandardFormRepository $standardFormRepository)
    {
        $this->standardFormRepository = $standardFormRepository;
    }


    /**
     * @var \RKW\RkwForm\Domain\Repository\FrontendUserRepository
     */
    public function injectFrontendUserRepository(FrontendUserRepository $frontendUserRepository)
    {
        $this->frontendUserRepository = $frontendUserRepository;
    }


    /**
     * @var \RKW\RkwForm\Domain\Repository\BackendUserRepository
     */
    public function injectBackendUserRepository(BackendUserRepository $backendUserRepository)
    {
        $this->backendUserRepository = $backendUserRepository;
    }


    /**
     * action initialize
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function initializeAction(): void
    {
        // workaround for a specific settings problem: Extbase does not longer distinguish between different plugin configurations
        // Second problem: If we would overwrite the whole settings array itself, the flexform settings would fly away. So let's merge
        // Hint: If a plugin has no specific settings, nothing further will happen. The standard settings would be used then
        $pluginSpecificSettings = $this->getPluginSettings();
        $this->settings = array_merge($this->settings, $pluginSpecificSettings);
    }


    /**
     * action new
     * @param \TYPO3\CMS\Extbase\DomainObject\AbstractEntity|null $standardForm
     * @return void
     */
    public function newAction(AbstractEntity $standardForm = null): void
    {
        $this->view->assign('standardForm', $standardForm);
    }


    /**
     * action createAbstract
     * (do not call this function directly. Add an "createAction" instead with specific Model param and specific validator)
     *
     * @param \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $standardForm
     * @param bool $privacy
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException if the slot is not valid
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException if a slot return
     */
    public function createAbstractAction(AbstractEntity $standardForm, bool $privacy = false): void
    {

        if (!$privacy) {
            $this->addFlashMessage(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                    'registrationController.error.accept_privacy', 'fe_register'
                ),
                '',
                AbstractMessage::ERROR
            );
            $this->forward('new', null, null, ['standardForm' => $standardForm]);
        }

        // give form to mailHandling function
        $this->mailHandling($standardForm);
        $this->standardFormRepository->add($standardForm);

    }


    /**
     * mail handling
     *
     * @param \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $formRequest
     * @return void
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException if the slot is not valid
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException if a slot return
     */
    protected function mailHandling(AbstractEntity $formRequest): void
    {
        /** @var \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser */
        $frontendUser = GeneralUtility::makeInstance(FrontendUser::class);
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
        $frontendUser->setTxFeregisterLanguageKey($GLOBALS['TSFE']->config['config']['language'] ?: 'de');

        /*
        // currently we do not use real privacy-entries
        if ($this->settings['includeFeRegisterPrivacy']) {
            // add privacy info
            \Madj2k\FeRegister\DataProtection\ConsentHandler::add($this->request, $frontendUser, $newSupportRequest, 'new support request');
        }
        */

        // send final confirmation mail to user
        $this->signalSlotDispatcher->dispatch(
            __CLASS__,
            self::SIGNAL_AFTER_REQUEST_CREATED_USER,
            [$frontendUser, $formRequest]
        );

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

        $this->signalSlotDispatcher->dispatch(
            __CLASS__,
            self::SIGNAL_AFTER_REQUEST_CREATED_ADMIN,
            [$backendUsers, $formRequest]
        );
    }


    /**
     * Remove ErrorFlashMessage
     *
     * @see \TYPO3\CMS\Extbase\Mvc\Controller\ActionController::getErrorFlashMessage()
     */
    protected function getErrorFlashMessage(): bool
    {
        return false;
    }


    /**
     * Returns plugin specific settings
     *
     * @param string $which Which type of settings will be loaded
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected function getPluginSettings(string $which = ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS): array
    {
        $pluginName = $this->request->getPluginName();
        return GeneralUtility::getTypoScriptConfiguration('Rkwform_'.$pluginName, $which);
    }
}
