<?php
namespace RKW\RkwForm\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use RKW\RkwForm\Domain\Model\GemCommunityForm;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

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
 * Class GemCommunityFormController
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class GemCommunityFormController extends \RKW\RkwForm\Controller\AbstractFormController
{

    /**
     * gemCommunityFormRepository
     *
     * @var \RKW\RkwForm\Domain\Repository\GemCommunityFormRepository
     * @inject
     */
    protected $gemCommunityFormRepository = null;

    /**
     * objectManager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     * @inject
     */
    protected $objectManager;

    /**
     * persistenceManager
     *
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;

    /**
     * action create
     *
     * @param \RKW\RkwForm\Domain\Model\GemCommunityForm $standardForm
     * @param int $privacy
     * @validate $standardForm \RKW\RkwForm\Validation\Validator\GemCommunityFormValidator
     * @return void
     */
    public function createAction(GemCommunityForm $standardForm, $privacy = 0)
    {

        /*
        if (!$standardForm->getBstAgree()) {
            $this->addFlashMessage(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                    'bstFormController.error.accept_agree', 'rkw_form'
                ),
                '',
                AbstractMessage::ERROR
            );
            $this->forward('new', null, null, array('standardForm' => $standardForm));
            //===
        }
        */

        //  set token
        $standardForm->setToken(sha1(rand()));

        //  set verificationLink -> muss aus der FlexForm kommen bzw. muss die gleiche Seite sein?
        $uri = $this->uriBuilder->reset()
            ->setTargetPageUid($GLOBALS["TSFE"]->id)
            ->uriFor(
                $actionName = 'verify',
                $controllerArguments = ['token' => $standardForm->getToken()],
                $controllerName = 'GemCommunityForm'
            );

        $standardForm->setVerificationUrl('###baseUrl###/' . $uri);

        //  set expiry date
        $standardForm->setValidUntil(strtotime("+24 hour"));  //  @todo: Das muss dynamisch sein und ggfs. aus der Flexform kommen!

        //  set identifier
        $standardForm->setIdentifier('gem-community');  //  @todo: Das muss dynamisch sein und ggfs. aus der Flexform kommen!

        //  @todo: Muss das immer gecheckt werden?
//        if (!$privacy) {
//            $this->addFlashMessage(
//                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
//                    'registrationController.error.accept_privacy', 'rkw_registration'
//                ),
//                '',
//                AbstractMessage::ERROR
//            );
//            $this->forward('new', null, null, array('standardForm' => $standardForm));
//            //===
//        }

        // give form to mailHandling function
        $this->sendVerificationLink($standardForm);
        $this->standardFormRepository->add($standardForm);

    }

    /**
     * action verify
     * @param string $token
     * @return void
     */
    public function verifyAction(string $token = '')
    {

        // set objects if they haven't been injected yet
        if (!$this->objectManager) {
            $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        }
        if (!$this->persistenceManager) {
            $this->persistenceManager = $this->objectManager->get(PersistenceManager::class);
        }

        if ($token) {

            $result = $this->gemCommunityFormRepository->findByToken($token);

            if ($result) {

                //  @todo: Do not verify, if already confirmed or expired!
                if ($result->getEnabled()) {

                    //  already confirmed
                    $this->addFlashMessage(
                        \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                            'gemCommunityFormController.error.verification.already_enabled', 'rkw_form'
                        ),
                        '',
                        AbstractMessage::INFO
                    );
                    $this->forward('confirmed', null, null, array('standardForm' => null));

                }

                if ($result->getValidUntil() < time()) {

                    DebuggerUtility::var_dump(date('d.m.Y H:i:s', $result->getValidUntil()));
                    DebuggerUtility::var_dump(date('d.m.Y H:i:s', time()));

                    DebuggerUtility::var_dump('expired');

                    $this->addFlashMessage(
                        \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                            'gemCommunityFormController.error.verification.expired', 'rkw_form'
                        ),
                        '',
                        AbstractMessage::ERROR
                    );
                    $this->forward('new', null, null, array('standardForm' => null));

                }

                //  set enabled
                $result->setEnabled(true);

                //  persist it
                $this->gemCommunityFormRepository->update($result);
                $this->persistenceManager->persistAll();

                //  @todo: Oder direkt löschen, nachdem die Bestätigung rausgeschickt wurde

                //  @todo: E-Mails an die Backend-User schicken
                $this->sendRegistrationToAdmins($result);

                //  @todo: Mit Bestätigung weiterleiten
//                if (!$standardForm->getBstAgree()) {
                    $this->addFlashMessage(
                        \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                            'gemCommunityFormController.error.verification.confirmed', 'rkw_form'
                        ),
                        '',
                        AbstractMessage::OK
                    );
                    $this->forward('confirmed', null, null, array('standardForm' => null));
                    //===
//                }

            } else {

                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'gemCommunityFormController.error.verification.notfound', 'rkw_form'
                    ),
                    '',
                    AbstractMessage::ERROR
                );
                $this->forward('new', null, null, array('standardForm' => null));

            }

        }

    }

    /**
     * action confirmed
     *
     * @return void
     */
    public function confirmedAction()
    {
        DebuggerUtility::var_dump('confirmed');
    }

    /**
     * mail handling
     * @param \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $formRequest
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException if the slot is not valid
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException if a slot return
     */
    protected function sendVerificationLink(AbstractEntity $formRequest)
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

        //  @todo: Mist, es ist notwendig, hier einen zweiten SignalSlot nur für diesen Controller anzulegen.
        // send final confirmation mail to user
        $this->signalSlotDispatcher->dispatch(__CLASS__, self::SIGNAL_AFTER_REQUEST_CREATED_USER, array($frontendUser, $formRequest));

    }

    /**
     * mail handling
     * @param \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $formRequest
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException if the slot is not valid
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException if a slot return
     */
    protected function sendRegistrationToAdmins(AbstractEntity $formRequest)
    {
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

}
