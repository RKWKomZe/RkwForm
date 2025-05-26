<?php
namespace RKW\RkwForm\Domain\Finishers;

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

use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Form\Domain\Model\FormElements\Page;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;
use TYPO3\CMS\Form\Domain\Model\FormElements\FileUpload;
use TYPO3\CMS\Form\Service\TranslationService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use Madj2k\CoreExtended\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * This finisher sends an email to one recipient
 *
 * Options:
 *
 * - templatePathAndFilename (mandatory): Template path and filename for the mail body
 * - layoutRootPath: root path for the layouts
 * - partialRootPath: root path for the partials
 * - variables: associative array of variables which are available inside the Fluid template
 *
 * The following options control the mail sending. In all of them, placeholders in the form
 * of {...} are replaced with the corresponding form value; i.e. {email} as recipientAddress
 * makes the recipient address configurable.
 *
 * - subject (mandatory): Subject of the email
 * - recipientAddress (mandatory): Email address of the recipient
 * - recipientName: Human-readable name of the recipient
 * - senderAddress (mandatory): Email address of the sender
 * - senderName: Human-readable name of the sender
 * - replyToAddress: Email address of to be used as reply-to email (use multiple addresses with an array)
 * - carbonCopyAddress: Email address of the copy recipient (use multiple addresses with an array)
 * - blindCarbonCopyAddress: Email address of the blind copy recipient (use multiple addresses with an array)
 * - format: format of the email (one of the FORMAT_* constants). By default mails are sent as HTML
 *
 */
class EmailFinisher extends \TYPO3\CMS\Form\Domain\Finishers\EmailFinisher
{

    /**
     * @var \TYPO3\CMS\Core\Database\Connection|null
     */
    protected ?Connection $databaseConnection = null;


    /**
     * Executes this finisher
     *
     * @return void
     * @throws FinisherException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     * @throws \TYPO3\CMS\Form\Domain\Exception\TypeDefinitionNotFoundException
     * @throws \TYPO3\CMS\Form\Domain\Exception\TypeDefinitionNotValidException
     * @see AbstractFinisher::execute()
     */
    protected function executeInternal(): void
    {

        $languageBackup = null;
        // Flexform overrides write strings instead of integers so
        // we need to cast the string '0' to false.
        if (
            isset($this->options['addHtmlPart'])
            && $this->options['addHtmlPart'] === '0'
        ) {
            $this->options['addHtmlPart'] = false;
        }

        $subject = $this->parseOption('subject');
        $recipients = $this->getRecipients('recipients', 'recipientAddress', 'recipientName');
        $senderAddress = $this->parseOption('senderAddress');
        $senderAddress = is_string($senderAddress) ? $senderAddress : '';
        $senderName = $this->parseOption('senderName');
        $senderName = is_string($senderName) ? $senderName : '';
        $replyToRecipients = $this->getRecipients('replyToRecipients', 'replyToAddress');
        $carbonCopyRecipients = $this->getRecipients('carbonCopyRecipients', 'carbonCopyAddress');
        $blindCarbonCopyRecipients = $this->getRecipients('blindCarbonCopyRecipients', 'blindCarbonCopyAddress');
        $addHtmlPart = $this->isHtmlPartAdded();
        $attachUploads = $this->parseOption('attachUploads');
        $useFluidEmail = $this->parseOption('useFluidEmail');
        $title = $this->parseOption('title');
        $title = is_string($title) && $title !== '' ? $title : $subject;

        if (empty($subject)) {
            throw new FinisherException('The option "subject" must be set for the EmailFinisher.', 1327060320);
        }
        if (empty($recipients)) {
            throw new FinisherException('The option "recipients" must be set for the EmailFinisher.', 1327060200);
        }
        if (empty($senderAddress)) {
            throw new FinisherException('The option "senderAddress" must be set for the EmailFinisher.', 1327060210);
        }

        $formRuntime = $this->finisherContext->getFormRuntime();

        $translationService = TranslationService::getInstance();
        if (is_string($this->options['translation']['language'] ?? null) && $this->options['translation']['language'] !== '') {
            $languageBackup = $translationService->getLanguage();
            $translationService->setLanguage($this->options['translation']['language']);
        }

        $mail = $useFluidEmail
            ? $this
                ->initializeFluidEmail($formRuntime)
                ->format($addHtmlPart ? FluidEmail::FORMAT_BOTH : FluidEmail::FORMAT_PLAIN)
                ->assign('title', $title)
            : \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(MailMessage::class);

        $mail
            ->from(new Address($senderAddress, $senderName))
            ->to(...$recipients)
            ->subject($subject);

        if (!empty($replyToRecipients)) {
            $mail->replyTo(...$replyToRecipients);
        }

        if (!empty($carbonCopyRecipients)) {
            $mail->cc(...$carbonCopyRecipients);
        }

        if (!empty($blindCarbonCopyRecipients)) {
            $mail->bcc(...$blindCarbonCopyRecipients);
        }

        if (!$useFluidEmail) {
            $parts = [
                [
                    'format' => 'Plaintext',
                    'contentType' => 'text/plain',
                ],
            ];

            if ($addHtmlPart) {
                $parts[] = [
                    'format' => 'Html',
                    'contentType' => 'text/html',
                ];
            }

            foreach ($parts as $i => $part) {
                $standaloneView = $this->initializeStandaloneView($formRuntime, $part['format']);

                //$message = $standaloneView->render();

                // ######### new RKW content START #########
                $settingsPostmaster = $this->getSettings('Postmaster');

                // replace baseURLs in final email  - replacement with assign only works in template-files, not on layout-files
                $message = preg_replace('/###baseUrl###/', rtrim($settingsPostmaster['baseUrl'], '/'), $standaloneView->render());
                $message = preg_replace('/###baseUrlImages###/', $this->getRelativePath(rtrim($settingsPostmaster['basePathImages'], '/')), $message);
                $message = preg_replace('/###baseUrlLogo###/', $this->getRelativePath(rtrim($settingsPostmaster['basePathLogo'], '/')), $message);

                /* @todo Check if Environment-variables are still valid in TYPO3 8.7 and upwards! */
                $replacePaths = [
                    GeneralUtility::getIndpEnv('TYPO3_SITE_PATH'),
                    $_SERVER['TYPO3_PATH_ROOT'] .'/'
                ];

                foreach ($replacePaths as $replacePath) {
                    $message = preg_replace(
                        '/(src|href)="' . str_replace('/', '\/', $replacePath) . '([^"]+)"/',
                        '$1="' . '/$2"',
                        $message
                    );
                }

                $message = preg_replace('/(src|href)="\/([^"]+)"/',
                    '$1="' . rtrim($settingsPostmaster['baseUrl'], '/') . '/$2"',
                    $message
                );

                // ######### new RKW content END #########

                if ($part['contentType'] === 'text/plain') {
                    $mail->text($message);
                } else {
                    $mail->html($message);
                }
            }
        }

        if (!empty($languageBackup)) {
            $translationService->setLanguage($languageBackup);
        }

        $elements = $formRuntime->getFormDefinition()->getRenderablesRecursively();

        if ($attachUploads) {
            foreach ($elements as $element) {
                if (!$element instanceof FileUpload) {
                    continue;
                }
                $file = $formRuntime[$element->getIdentifier()];
                if ($file) {
                    if ($file instanceof FileReference) {
                        $file = $file->getOriginalResource();
                    }

                    $mail->attach($file->getContents(), $file->getName(), $file->getMimeType());
                }
            }
        }

        $useFluidEmail ? GeneralUtility::makeInstance(Mailer::class)->send($mail) : $mail->send();
    }


    /**
     * Returns the relative image path
     *
     * @param string $path
     * @return string
     */
    protected function getRelativePath(string $path): string
    {

        if (strpos($path, 'EXT:') === 0) {

            list($extKey, $local) = explode('/', substr($path, 4), 2);

            if (
                ((string)$extKey !== '')
                && (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded($extKey))
                && ((string)$local !== '')
            ) {
                $path = PathUtility::getAbsoluteWebPath(ExtensionManagementUtility::extPath($extKey) . $local);
                if (strpos($path, '../') === 0) {
                    $path = substr($path, -(strlen($path)-3));
                }
            }
        }

        return $path;
    }


    /**
     * Returns TYPO3 settings
     *
     * @param string $extension Extension name of needed typoscript data
     * @param string $which Which type of settings will be loaded
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected function getSettings(
        string $extension = 'RkwForm',
        string $which = ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
    ): array {

        return GeneralUtility::getTypoScriptConfiguration($extension, $which);
    }

}
