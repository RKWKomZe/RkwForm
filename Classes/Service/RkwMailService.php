<?php
namespace RKW\RkwForm\Service;

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

use Madj2k\CoreExtended\Utility\GeneralUtility;
use Madj2k\Postmaster\Mail\MailMessage;
use RKW\RkwForm\Domain\Model\StandardForm;
use Madj2k\Postmaster\Mail\MailMassage;
use Madj2k\FeRegister\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * RkwMailService
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class RkwMailService implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * Sends an E-Mail to a Frontend-User
     *
     * @param \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser
     * @param \RKW\RkwForm\Domain\Model\StandardForm $formRequest
     * @return void
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function userMail(FrontendUser $frontendUser, StandardForm $formRequest): void
    {
        // get settings
        $settings = $this->getSettings(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $settingsDefault = $this->getSettings();

        if ($frontendUser->getEmail()) {
            if ($settings['view']['templateRootPaths'][0]) {

                /** @var \Madj2k\Postmaster\Mail\MailMessage $mailService */
                $mailService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(MailMessage::class);

                // send new user an email with token
                $mailService->setTo($frontendUser, array(
                    'marker' => array(
                        'formRequest' => $formRequest,
                        'frontendUser' => $frontendUser,
                        'pageUid'      => intval($GLOBALS['TSFE']->id),
                        'loginPid'     => intval($settingsDefault['loginPid']),
                    ),
                ));

                $mailService->getQueueMail()->setSubject(
                    \Madj2k\Postmaster\Utility\FrontendLocalizationUtility::translate(
                        'rkwMailService.confirmationUser.subject',
                        'rkw_form',
                        null,
                        $frontendUser->getTxFeregisterLanguageKey()
                    )
                );

                $mailService->getQueueMail()->addTemplatePaths($settings['view']['templateRootPaths']);
                $mailService->getQueueMail()->addPartialPaths($settings['view']['partialRootPaths']);

                $mailService->getQueueMail()->setPlaintextTemplate('Email/ConfirmationUser');
                $mailService->getQueueMail()->setHtmlTemplate('Email/ConfirmationUser');

                $mailService->send();
            }
        }

    }


    /**
     * Sends a verification E-Mail to a Frontend-User
     *
     * @param \RKW\RkwForm\Domain\Model\BackendUser|array $backendUser
     * @param \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser
     * @param \RKW\RkwForm\Domain\Model\StandardForm $formRequest
     *
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function verifyMail($backendUser, \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser, \RKW\RkwForm\Domain\Model\StandardForm $formRequest)
    {
        // get settings
        $settings = $this->getSettings(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $settingsDefault = $this->getSettings();

        $recipients = $this->getBackendRecipients($backendUser);

        if ($frontendUser->getEmail()) {
            if ($settings['view']['templateRootPaths'][0]) {

                /** @var \Madj2k\Postmaster\Mail\MailMessage $mailService */
                $mailService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(MailMessage::class);

                // send new user an email with token
                $mailService->setTo($frontendUser, array(
                    'marker' => array(
                        'formRequest'  => $formRequest,
                        'frontendUser' => $frontendUser,
                        'pageUid'      => intval($GLOBALS['TSFE']->id),
                        'loginPid'     => intval($settingsDefault['loginPid']),
                    ),
                ));

                $mailService->getQueueMail()->setSubject(
                    \Madj2k\Postmaster\Utility\FrontendLocalizationUtility::translate(
                        'rkwMailService.verifyUser.subject',
                        'rkw_form',
                        null,
                        $frontendUser->getTxFeregisterLanguageKey()
                    )
                );

                if ($recipients[0]->getRealName()) {
                    $mailService->getQueueMail()->setReplyToName($recipients[0]->getRealName());
                }
                $mailService->getQueueMail()->setReplyToAddress($recipients[0]->getEmail());

                $mailService->getQueueMail()->addTemplatePaths($settings['view']['templateRootPaths']);
                $mailService->getQueueMail()->addPartialPaths($settings['view']['partialRootPaths']);

                $mailService->getQueueMail()->setPlaintextTemplate('Email/GemCommunityForm/VerifyUser');
                $mailService->getQueueMail()->setHtmlTemplate('Email/GemCommunityForm/VerifyUser');

                $mailService->send();
            }
        }

    }


    /**
     * Sends an E-Mail to an Admin
     *
     * @param \RKW\RkwForm\Domain\Model\BackendUser|array $backendUser
     * @param \RKW\RkwForm\Domain\Model\StandardForm $formRequest
     * @return void
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function adminMail($backendUser, StandardForm $formRequest): void
    {
        // get settings
        $settings = $this->getSettings(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $settingsDefault = $this->getSettings();

        $recipients = array();
        if (is_array($backendUser)) {
            $recipients = $backendUser;
        } else {
            $recipients[] = $backendUser;
        }

        if ($settings['view']['templateRootPaths'][0]) {

            /** @var \Madj2k\Postmaster\Mail\MailMessage $mailService */
            $mailService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(MailMessage::class);

            foreach ($recipients as $recipient) {
                if (
                    ($recipient instanceof \RKW\RkwForm\Domain\Model\BackendUser)
                    && ($recipient->getEmail())
                ) {

                    // send new user an email with token
                    $mailService->setTo($recipient, array(
                        'marker'  => array(
                            'formRequest' => $formRequest,
                            'backendUser'  => $recipient,
                            'pageUid'      => intval($GLOBALS['TSFE']->id),
                            'loginPid'     => intval($settingsDefault['loginPid']),
                        ),
                        'subject' => \Madj2k\Postmaster\Utility\FrontendLocalizationUtility::translate(
                            'rkwMailService.notifyAdmin.subject',
                            'rkw_form',
                            null,
                            $recipient->getLang()
                        ),
                    ));
                }
            }

            if ($formRequest->getEmail()) {
                $mailService->getQueueMail()->setReplyAddress($formRequest->getEmail());
            }

            $mailService->getQueueMail()->setSubject(
                \Madj2k\Postmaster\Utility\FrontendLocalizationUtility::translate(
                    'rkwMailService.notifyAdmin.subject',
                    'rkw_form',
                    null,
                    'de'
                )
            );

            $mailService->getQueueMail()->addTemplatePaths($settings['view']['templateRootPaths']);
            $mailService->getQueueMail()->addPartialPaths($settings['view']['partialRootPaths']);

            $mailService->getQueueMail()->setPlaintextTemplate('Email/NotifyAdmin');
            $mailService->getQueueMail()->setHtmlTemplate('Email/NotifyAdmin');

            if (count($mailService->getTo())) {
                $mailService->send();
            }
        }
    }


    /**
     * Sends a notification about gem community registration to an admin
     *
     * @param \RKW\RkwForm\Domain\Model\BackendUser|array $backendUser
     * @param \RKW\RkwForm\Domain\Model\StandardForm $formRequest
     *
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function adminNotificationMail($backendUser, \RKW\RkwForm\Domain\Model\StandardForm $formRequest)
    {
        // get settings
        $settings = $this->getSettings(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $settingsDefault = $this->getSettings();

        $recipients = $this->getBackendRecipients($backendUser);

        if ($settings['view']['templateRootPaths'][0]) {

            /** @var \Madj2k\Postmaster\Mail\MailMessage $mailService */
            $mailService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(MailMessage::class);

            foreach ($recipients as $recipient) {
                if (
                    ($recipient instanceof \RKW\RkwForm\Domain\Model\BackendUser)
                    && ($recipient->getEmail())
                ) {
                    // send new user an email with token
                    $mailService->setTo($recipient, array(
                        'marker'  => array(
                            'formRequest'  => $formRequest,
                            'backendUser'  => $recipient,
                        ),
                    ));
                }
            }

            $mailService->getQueueMail()->setSubject(
                \Madj2k\Postmaster\Utility\FrontendLocalizationUtility::translate(
                    'rkwMailService.gemCommunityForm.notifyAdmin.subject',
                    'rkw_form',
                    [$formRequest->getFirstName() . ' ' . $formRequest->getLastName()],
                    $recipient->getLang()
                )
            );

            if (
                ($formRequest->getEmail())
            ) {
                $mailService->getQueueMail()->setReplyToAddress($formRequest->getEmail());
            }

            $mailService->getQueueMail()->addTemplatePaths($settings['view']['templateRootPaths']);
            $mailService->getQueueMail()->addPartialPaths($settings['view']['partialRootPaths']);

            $mailService->getQueueMail()->setPlaintextTemplate('Email/GemCommunityForm/NotifyAdmin');
            $mailService->getQueueMail()->setHtmlTemplate('Email/GemCommunityForm/NotifyAdmin');

            if (count($mailService->getTo())) {
                $mailService->send();
            }
        }
    }


    /**
     * Returns TYPO3 settings
     *
     * @param string $which Which type of settings will be loaded
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected function getSettings(string $which = ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS): array
    {
        return GeneralUtility::getTypoScriptConfiguration('Rkwform', $which);
    }


    /**
     * @param $backendUser
     * @return array
     */
    protected function getBackendRecipients($backendUser): array
    {
        $recipients = array();
        if (is_array($backendUser)) {
            $recipients = $backendUser;
        } else {
            $recipients[] = $backendUser;
        }

        return $recipients;
    }
}
