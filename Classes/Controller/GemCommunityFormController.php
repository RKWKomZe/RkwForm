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

use RKW\RkwForm\Domain\Model\GemCommunityForm;
use RKW\RkwForm\Domain\Model\StandardForm;
use RKW\RkwRegistration\Domain\Model\FrontendUser;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class GemCommunityFormController
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class GemCommunityFormController extends AbstractFormController
{
    /**
     * gemCommunityFormRepository
     *
     * @var \RKW\RkwForm\Domain\Repository\GemCommunityFormRepository
     * @inject
     */
    protected $gemCommunityFormRepository;

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
     *
     * @param \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $standardForm
     * @return void
     */
    public function newAction(AbstractEntity $standardForm = null): void
    {
        $this->view->assignMultiple([
            'standardForm' => $standardForm,
            'topics' => GeneralUtility::trimExplode(",", $this->settings['form']['topics'])
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
    public function createAction(GemCommunityForm $standardForm, int $privacy = 0, int $terms = 0): void
    {

        if (!$privacy) {
            $this->addFlashMessage(
                LocalizationUtility::translate(
                    'registrationController.error.accept_privacy', 'rkw_registration'
                ),
                '',
                AbstractMessage::ERROR
            );
            $this->forward('new', null, null, ['standardForm' => $standardForm]);
        }

        if (!$terms) {
            $this->addFlashMessage(
                LocalizationUtility::translate(
                    'gemCommunityFormController.error.accept_terms', 'rkw_form'
                ),
                '',
                AbstractMessage::ERROR
            );
            $this->forward('new', null, null, ['standardForm' => $standardForm]);
        }

        $standardForm->setToken(sha1(rand()));

        $uri = $this->uriBuilder->reset()
            ->setTargetPageUid($GLOBALS["TSFE"]->id)
            ->uriFor(
                'verify',
                ['token' => $standardForm->getToken()],
                'GemCommunityForm'
            );

        $standardForm->setVerificationUrl('###baseUrl###/' . $uri);
        $standardForm->setValidUntil(strtotime($this->settings['verification']['validUntil']));
        $standardForm->setIdentifier($this->settings['form']['identifier']);

        // pass form to mailHandling function
        $this->sendVerificationLink($standardForm);
        $this->standardFormRepository->add($standardForm);

    }

    /**
     * action verify
     *
     * @param string $token
     * @return void
     */
    public function verifyAction(string $token = ''): void
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

            if ($result->getFirst()) {

                /* @var \RKW\RkwForm\Domain\Model\GemCommunityForm $standardForm */
                $standardForm = $result->getFirst();

                if ($standardForm->getEnabled()) {
                    //  already confirmed
                    $this->addFlashMessage(
                        LocalizationUtility::translate(
                            'gemCommunityFormController.error.verification.already_enabled', 'rkw_form'
                        ),
                        '',
                        AbstractMessage::INFO
                    );
                    $this->forward('confirmed', null, null, ['standardForm' => null]);
                }

                if ($standardForm->getValidUntil() < time()) {
                    $this->addFlashMessage(
                        LocalizationUtility::translate(
                            'gemCommunityFormController.error.verification.expired', 'rkw_form'
                        ),
                        '',
                        AbstractMessage::ERROR
                    );
                    $this->forward('new', null, null, ['standardForm' => null]);
                }

                $standardForm->setEnabled(true);

                $this->gemCommunityFormRepository->update($standardForm);
                $this->persistenceManager->persistAll();

                $this->sendRegistrationToAdmins($standardForm);

                $this->addFlashMessage(
                    LocalizationUtility::translate(
                        'gemCommunityFormController.error.verification.confirmed', 'rkw_form'
                    ),
                    '',
                    AbstractMessage::OK
                );
                $this->forward('confirmed', null, null, ['standardForm' => null]);

            } else {

                $this->addFlashMessage(
                    LocalizationUtility::translate(
                        'gemCommunityFormController.error.verification.notfound', 'rkw_form'
                    ),
                    '',
                    AbstractMessage::ERROR
                );
                $this->forward('new', null, null, ['standardForm' => null]);
            }

        }
    }


    /**
     * action confirmed
     *
     * @return void
     */
    public function confirmedAction(): void
    {
        //
    }


    /**
     * mail handling
     *
     * @param \RKW\RkwForm\Domain\Model\GemCommunityForm $standardForm
     * @return void
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException if the slot is not valid
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException if a slot return
     */
    protected function sendVerificationLink(GemCommunityForm $standardForm): void
    {

        /** @var \RKW\RkwRegistration\Domain\Model\FrontendUser $frontendUser */
        $frontendUser = GeneralUtility::makeInstance(FrontendUser::class);
        $frontendUser->setEmail($standardForm->getEmail());
        $frontendUser->setFirstName($standardForm->getFirstName());
        $frontendUser->setLastName($standardForm->getLastName());
        if (
            ($standardForm->getTitle())
            && ($standardForm->getTitle()->getIsIncludedInSalutation())
            && (! $standardForm->getTitle()->getIsTitleAfter())
        ) {
            $frontendUser->setTitle($standardForm->getTitle()->getName());
        }

        $frontendUser->setTxRkwregistrationLanguageKey($GLOBALS['TSFE']->config['config']['language'] ?: 'de');

        /*
        // currently we do not use real privacy-entries
        if ($this->settings['includeRkwRegistrationPrivacy']) {
            // add privacy info
            \RKW\RkwRegistration\Tools\Privacy::addPrivacyData($this->request, $frontendUser, $newSupportRequest, 'new support request');
        }
        */

        $backendUsers = $this->getBackendUsers();

        // send mail with verification link to user
        $this->signalSlotDispatcher->dispatch(
            __CLASS__,
            self::SIGNAL_AFTER_REQUEST_CREATED_USER,
            [$backendUsers, $frontendUser, $standardForm]
        );
    }


    /**
     * mail handling
     *
     * @param \RKW\RkwForm\Domain\Model\GemCommunityForm $standardForm
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException if the slot is not valid
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException if a slot return
     */
    protected function sendRegistrationToAdmins(StandardForm $standardForm): void
    {
        $backendUsers = $this->getBackendUsers();

        $this->signalSlotDispatcher->dispatch(
            __CLASS__,
            self::SIGNAL_AFTER_REQUEST_CREATED_ADMIN,
            [$backendUsers, $standardForm]
        );
    }


    /**
     * @return array
     */
    protected function getBackendUsers(): array
    {
        $adminUidList = explode(',', $this->settings['mail']['backendUser']);
        $backendUsers = [];
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
