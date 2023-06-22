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

use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
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
        $formRuntime = $this->finisherContext->getFormRuntime();
        $standaloneView = $this->initializeStandaloneView($formRuntime);

        $translationService = TranslationService::getInstance();
        if (isset($this->options['translation']['language']) && !empty($this->options['translation']['language'])) {
            $languageBackup = $translationService->getLanguage();
            $translationService->setLanguage($this->options['translation']['language']);
        }

        if ($formRuntime->getFormDefinition()->getIdentifier() === 'gem-community-confirm') {

            $this->databaseConnection = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable('tx_rkwform_domain_model_standardform');

            // find go through all pages
            /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
            $queryBuilder = $this->databaseConnection->createQueryBuilder();
            $statement = $queryBuilder->select('*')
                ->from('tx_rkwform_domain_model_standardform')
                ->where(
                    $queryBuilder->expr()->eq('token',
                        $queryBuilder->createNamedParameter($formRuntime['gettoken'], \PDO::PARAM_STR)
                    )
                )
                ->execute();

            $renderables = $formRuntime->getFormDefinition()->getRenderablesRecursively();

            $page = null;
            foreach ($renderables as $renderable) {
                if ($renderable instanceof Page) {
                    $page = $renderable;
                }
            }

            if ($page) {
                foreach ($page->getRenderablesRecursively() as $renderable) {

                    if ($renderable->getIdentifier() === 'gettoken') {
                        $page->removeElement($renderable);
                    }

                }

                if ($statement) {

                    $record = $statement->fetchAll()[0];

                    /** @var \TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader $yamlFileLoader */
                    $yamlFileLoader = GeneralUtility::makeInstance(YamlFileLoader::class);
                    $formConfiguration = $yamlFileLoader->load('EXT:rkw_form/Configuration/Yaml/Forms/gem-community.form.yaml');

                    $includableElements = [
                        'salutation',
                        'title',
                        'first_name',
                        'last_name',
                        'phone',
                        'email',
                        'company',
                        'street',
                        'postal',
                        'city',
                        'theme',
                    ];

                    $fillableElements = array_filter(
                        $formConfiguration['renderables'][0]['renderables'],
                        function($element) use ($includableElements) {
                            return in_array($element['identifier'], $includableElements);
                        }
                    );

                    foreach ($fillableElements as $element) {

                        $key = $element['identifier'];

                        $fillable = $page->createElement($key, 'Text');
                        $fillable->setLabel($element['label']);

                        if ($key === 'salutation') {
                            $fillable->setDefaultValue($translationService->translate(
                                'LLL:EXT:rkw_form/Resources/Private/Language/locallang.xlf:tx_rkwform_domain_model_standardform.salutation.I.' . $record[$key])
                            );
                        } else {
                            $fillable->setDefaultValue($record[$key]);
                        }
                    }
                }
            }
        }

        // this line is replaced through following lines
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

        if (!empty($languageBackup)) {
            $translationService->setLanguage($languageBackup);
        }

        $subject = $this->parseOption('subject');
        $recipientAddress = $this->parseOption('recipientAddress');
        $recipientName = $this->parseOption('recipientName');
        $senderAddress = $this->parseOption('senderAddress');
        $senderName = $this->parseOption('senderName');
        $replyToAddress = $this->parseOption('replyToAddress');
        $carbonCopyAddress = $this->parseOption('carbonCopyAddress');
        $blindCarbonCopyAddress = $this->parseOption('blindCarbonCopyAddress');
        $format = $this->parseOption('format');
        $attachUploads = $this->parseOption('attachUploads');

        if (empty($subject)) {
            throw new FinisherException('The option "subject" must be set for the EmailFinisher.', 1327060320);
        }
        if (empty($recipientAddress)) {
            throw new FinisherException('The option "recipientAddress" must be set for the EmailFinisher.', 1327060200);
        }
        if (empty($senderAddress)) {
            throw new FinisherException('The option "senderAddress" must be set for the EmailFinisher.', 1327060210);
        }

        /** @var \TYPO3\CMS\Core\Mail\MailMessage $mail */
        $mail = $this->objectManager->get(MailMessage::class);

        $mail->setFrom([$senderAddress => $senderName])
            ->setTo([$recipientAddress => $recipientName])
            ->setSubject($subject);

        if (!empty($replyToAddress)) {
            $mail->setReplyTo($replyToAddress);
        }

        if (!empty($carbonCopyAddress)) {
            $mail->setCc($carbonCopyAddress);
        }

        if (!empty($blindCarbonCopyAddress)) {
            $mail->setBcc($blindCarbonCopyAddress);
        }

        if ($format === self::FORMAT_PLAINTEXT) {
            $mail->setBody($message, 'text/plain');
        } else {
            $mail->setBody($message, 'text/html');
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

                    $mail->attach(\Swift_Attachment::newInstance($file->getContents(), $file->getName(), $file->getMimeType()));
                }
            }
        }

        $mail->send();
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
