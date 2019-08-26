<?php
namespace RKW\RkwForm\Controller;
use \RKW\RkwForm\Domain\Model\Standard;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/***
 *
 * This file is part of the "RkwForm" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Maximilian Fäßler <maximilian@faesslerweb.de>, Fäßler Web UG
 *
 ***/

/**
 * StandardController
 */
class StandardController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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
     * standardRepository
     *
     * @var \RKW\RkwForm\Domain\Repository\StandardRepository
     * @inject
     */
    protected $standardRepository = null;

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
     * action new
     * @param \RKW\RkwForm\Domain\Model\Standard $newStandard
     * @return void
     */
    public function newAction(Standard $newStandard = null)
    {
        $this->view->assign('newStandard', $newStandard);
    }



    /**
     * action create
     *
     * @param \RKW\RkwForm\Domain\Model\Standard $newStandard
     * @param int $privacy
     * @validate $newStandard \RKW\RkwForm\Validation\Validator\StandardValidator
     * @return void
     */
    public function createAction(Standard $newStandard, $privacy = 0)
    {
        if (!$privacy) {
            $this->addFlashMessage(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                    'registrationController.error.accept_privacy', 'rkw_registration'
                ),
                '',
                AbstractMessage::ERROR
            );
            $this->forward('new', null, null, array('newStandard' => $newStandard));
            //===
        }

        // give form to mailHandling function
        $this->mailHandling($newStandard);

        $this->standardRepository->add($newStandard);

        // Final: Show create page with text
    }


    /**
     * mail handling
     * @param mixed $formRequest
     *
     */
    private function mailHandling($formRequest)
    {
        /** @var \RKW\RkwRegistration\Domain\Model\FrontendUser $frontendUser */
        $frontendUser = GeneralUtility::makeInstance('RKW\\RkwRegistration\\Domain\\Model\\FrontendUser');
        $frontendUser->setEmail($formRequest->getEmail());
        $frontendUser->setFirstName($formRequest->getFirstName());
        $frontendUser->setLastName($formRequest->getLastName());

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
            && ($backendUserFallback = intval($this->settings['backendUserIdForMails']))
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
