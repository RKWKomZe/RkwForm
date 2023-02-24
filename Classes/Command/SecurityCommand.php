<?php
namespace RKW\RkwForm\Command;

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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SecurityCommand
 *
 * Execute on CLI with: 'vendor/bin/typo3 rkw_form:security'
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SecurityCommand extends Command
{

    /**
     * @var string
     */
    protected string $defaultUploadFolder = '1:/user_upload/tx_rkwfeecalculator';

    /**
     * @var \TYPO3\CMS\Core\Log\Logger|null
     */
    protected ?Logger $logger = null;


    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure(): void
    {
        $this->setDescription('Security check for request folder.');
    }


    /**
     * Initializes the command after the input has been bound and before the input
     * is validated.
     *
     * This is mainly useful when a lot of commands extends one main command
     * where some things need to be initialized based on the input arguments and options.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @see \Symfony\Component\Console\Input\InputInterface::bind()
     * @see \Symfony\Component\Console\Input\InputInterface::validate()
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        // nothing to do
    }


    /**
     * Executes the command for showing sys_log entries
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @see \Symfony\Component\Console\Input\InputInterface::bind()
     * @see \Symfony\Component\Console\Input\InputInterface::validate()
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $result = 0;
        try {

            /** @var \TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader $yamlFileLoader */
            $yamlFileLoader = GeneralUtility::makeInstance(YamlFileLoader::class);
            $formConfiguration = $yamlFileLoader->load('EXT:rkw_form/Configuration/Yaml/FormFrameworkConf.yaml');

            $filePathArray = $formConfiguration['TYPO3']['CMS']['Form']['persistenceManager']['allowedFileMounts'];


            foreach ($filePathArray as $filePath) {

                // Yes: Remove ":" AND "/" (the slash comes back through $GLOBALS['TYPO3_CONF_VARS']['BE']['fileadminDir']
                $filePathSplit = GeneralUtility::trimExplode(':/', $filePath);
                $filePath = GeneralUtility::getFileAbsFileName(
                    $GLOBALS['TYPO3_CONF_VARS']['BE']['fileadminDir']
                    . $filePathSplit[1]
                );

                // ERROR Check if path does not exists
                if (!is_dir($filePath)) {
                    $message = sprintf('Given file path does not exist=%s.', $filePath);

                    $io->error($message);
                    $this->getLogger()->log(LogLevel::ERROR, $message);
                    continue;
                }

                // WARNING Disallow ALL paths without string "tx_rkwform" in it! Just for secure. Anything could be defined via
                // the ExtForm-Yaml file
                if (strpos($filePath, 'tx_rkwform') === false) {

                    $message = sprintf(
                        'Rejected: The given path does not contains the string "tx_rkwform": %s.',
                        $filePath
                    );
                    $io->warning($message);
                    $this->getLogger()->log(LogLevel::WARNING, $message);
                    continue;
                }

                if ($this->securityCheck($filePath)) {

                    $message = sprintf('Upload path "%s" is secure.', $filePath );
                    $io->note($message);
                    $this->getLogger()->log(LogLevel::INFO, $message);
                    $result = 1;

                } else {
                    $message = sprintf('Upload path "%s" is NOT secure. Fix this immediately!', $filePath );
                    $io->error($message);
                    $this->getLogger()->log(LogLevel::ERROR, $message);
                }
            }

        } catch (\Exception $e) {

            $message = sprintf('An unexpected error occurred while trying security-check: %s',
                str_replace(array("\n", "\r"), '', $e->getMessage())
            );

            $io->error($message);
            $this->getLogger()->log(LogLevel::ERROR, $message);
        }

        $io->writeln('Done');
        return $result;

    }

    /**
     * .htaccess-based protection for the file folder
     * @param string $filePath
     * @return bool
     */
    protected function securityCheck(string $filePath): bool
    {

        $pathToApacheFile =  $filePath . '.htaccess';
        $pathToNginxFile =  $filePath . 'conf.nginx';

        if (! file_exists($pathToApacheFile)) {

            $content = '# This file is automatically generated.' . "\n" .
                '# Please to not modify it manually because all changes may be lost.' . "\n\n" .

                '# Apache < 2.3' . "\n" .
                '<IfModule !mod_authz_core.c>'. "\n" .
                "\t" . 'Order allow,deny'. "\n" .
                "\t" . 'Deny from all'. "\n" .
                "\t" . 'Satisfy All'. "\n" .
                '</IfModule>'. "\n\n" .

                '# Apache ≥ 2.3' . "\n" .
                '<IfModule mod_authz_core.c>'. "\n" .
                "\t" . 'Require all denied' . "\n" .
                '</IfModule>';

            if (! file_put_contents($pathToApacheFile, $content)) {
                return false;
            }
        }

        // create .htaccess if there is none!
        if (! file_exists($pathToNginxFile)) {

            $content = '# This file is automatically generated.' . "\n" .
                '# Please to not modify it manually because all changes may be lost.' . "\n\n" .
                'deny all;' . "\n" .
                'satisfy all;';

            if (!file_put_contents($pathToNginxFile, $content)) {
                return false;
            }
        }

        return true;
    }


    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger(): Logger
    {
        if (!$this->logger instanceof \TYPO3\CMS\Core\Log\Logger) {
            $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        }

        return $this->logger;
    }
}
