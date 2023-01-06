<?php
namespace RKW\RkwForm\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use RKW\RkwForm\Domain\Model\GemCommunityForm;
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
 * @copyright RKW Kompetenzzentrum
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
     * action new
     * @param \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $standardForm
     * @return void
     */
    public function newAction(AbstractEntity $standardForm = null)
    {
        $this->view->assignMultiple([
            'standardForm' => $standardForm,
            'topics' => \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(",", $this->settings['form']['topics'])
        ]);
    }

    /**
     * action create
     *
     * @param \RKW\RkwForm\Domain\Model\GemCommunityForm $standardForm
     * @param int $privacy
     * @param int $terms
     * @validate $standardForm \RKW\RkwForm\Validation\Validator\GemCommunityFormValidator
     * @return void
     */
    public function createAction(GemCommunityForm $standardForm, $privacy = 0, $terms = 0)
    {

        //  @todo: Muss das immer gecheckt werden?
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

        if (!$terms) {
            $this->addFlashMessage(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                    'gemCommunityFormController.error.accept_terms', 'rkw_form'
                ),
                '',
                AbstractMessage::ERROR
            );
            $this->forward('new', null, null, array('standardForm' => $standardForm));
            //===
        }

        $standardForm->setToken(sha1(rand()));

        $uri = $this->uriBuilder->reset()
            ->setTargetPageUid($GLOBALS["TSFE"]->id)
            ->uriFor(
                $actionName = 'verify',
                $controllerArguments = ['token' => $standardForm->getToken()],
                $controllerName = 'GemCommunityForm'
            );

        $standardForm->setVerificationUrl('###baseUrl###/' . $uri);
        $standardForm->setValidUntil(strtotime($this->settings['verification']['validUntil']));
        $standardForm->setIdentifier($this->settings['identifier']);

        // pass form to mailHandling function
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

                $this->sendRegistrationToAdmins($result);

                //  @todo: Mit Bestätigung weiterleiten
                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'gemCommunityFormController.error.verification.confirmed', 'rkw_form'
                    ),
                    '',
                    AbstractMessage::OK
                );
                $this->forward('confirmed', null, null, array('standardForm' => null));
                //===

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
        // @todo muss ich hier was tun bzw. ist da überhaupt ne eigene Action notwendig?
//        DebuggerUtility::var_dump('confirmed');
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

        $backendUsers = $this->getBackendUsers();

        // send mail with verification link to user
        $this->signalSlotDispatcher->dispatch(__CLASS__, self::SIGNAL_AFTER_REQUEST_CREATED_USER, array($backendUsers, $frontendUser, $formRequest));

    }

    /**
     * mail handling
     * @param \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $formRequest
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException if the slot is not valid
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException if a slot return
     */
    protected function sendRegistrationToAdmins(AbstractEntity $formRequest)
    {
        $backendUsers = $this->getBackendUsers();

        $this->signalSlotDispatcher->dispatch(__CLASS__, self::SIGNAL_AFTER_REQUEST_CREATED_ADMIN, array($backendUsers, $formRequest));
    }

    /**
     * @return array
     */
    protected function getBackendUsers(): array
    {
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

        return $backendUsers;
    }

}
