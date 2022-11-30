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

use Doctrine\Common\Util\Debug;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Version\Dependency\DependencyEntityFactory;

/**
 * CommandController
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController
{

    /**
     * standardFormRepository
     *
     * @var \RKW\RkwForm\Domain\Repository\StandardFormRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $standardFormRepository;

    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

    /**
     * Clean up for form uploads
     *
     * @param integer $daysFromNow Defines which old files should be deleted
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function cleanupCommand($daysFromNow = 30)
    {
        $cleanupTimestamp = time() - (intval($daysFromNow) * 24 * 60 * 60);

        /** @var \TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader $yamlFileLoader */
        $yamlFileLoader = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Configuration\\Loader\\YamlFileLoader');
        $formConfiguration = $yamlFileLoader->load('EXT:rkw_form/Configuration/Yaml/FormFrameworkConf.yaml');

        $filePathArray = $formConfiguration['TYPO3']['CMS']['Form']['persistenceManager']['allowedFileMounts'];

        foreach ($filePathArray as $filePath) {

            // Yes: Remove ":" AND "/" (the slash comes back through $GLOBALS['TYPO3_CONF_VARS']['BE']['fileadminDir']
            $filePathSplit = GeneralUtility::trimExplode(':/', $filePath);

            $filePath = GeneralUtility::getFileAbsFileName($GLOBALS['TYPO3_CONF_VARS']['BE']['fileadminDir'] . $filePathSplit[1]);

            // ERROR Check if path does not exists
            if (!is_dir($filePath)) {
                $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('Given file path does not exists=%s.', $filePath));
                continue;
            }

            // WARNING Disallow ALL paths without string "tx_rkwform" in it! Just for secure. Anything could be defined via
            // the ExtForm-Yaml file
            if (strpos($filePath, 'tx_rkwform') === false) {
                $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::WARNING, sprintf('Rejected: The given path does not contains the string "tx_rkwform"=%s.', $filePath));
                continue;
            }

            $this->securityCheck($filePath);

            // get all files from given path
            $fileList = GeneralUtility::getFilesInDir($filePath);

            $counter = 0;
            foreach ($fileList as $file) {

                if (
                    $daysFromNow === 0
                    || (
                        $cleanupTimestamp > 0
                        && ($cleanupTimestamp > filemtime($filePath . $file)
                        )
                    )
                ) {
                    $counter++;
                    // remove file from disk
                    unlink($filePath . $file);
                }
            }

            // Message with X files were deleted
            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Cleanup command runs successfully. A total of %s files were deleted from %s.', $counter, $filePath));
        }
    }

    /**
     * Cleanup expired records
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function cleanupOptinCommand()
    {

        try {

            $expiredRecords = $this->standardFormRepository->findExpiredByFormIdentifier('gem-community');

            $cnt = 0;
            foreach ($expiredRecords as $expiredRecord) {
                $this->standardFormRepository->remove($expiredRecord);
                $cnt++;
            }

            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Successfully removed %s expired form records completely from the database.', $cnt));

        } catch (\Exception $e) {
            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, sprintf('An error occurred while trying to remove expired form records completely from the database. Message: %s', str_replace(array("\n", "\r"), '', $e->getMessage())));
        }


        //  delete all of them

//        // Message with X files were deleted
//        $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Cleanup command runs successfully. A total of %s files were deleted from %s.', $counter, $filePath));

    }

    /**
     * .htaccess-based protection for the file folder
     *
     * @return bool
     */
    protected function securityCheck($filePath) {

        $pathToFile =  $filePath . '.htaccess';

        // create .htaccess if there is none!
        if (file_exists($pathToFile)) {
            return true;
        }

        $htaccessContent = '
# This file is automatically generated. Please to not modify it manually because all changes may be lost.

# Apache < 2.3
<IfModule !mod_authz_core.c>
Order allow,deny
Deny from all
Satisfy All
</IfModule>

# Apache ≥ 2.3
<IfModule mod_authz_core.c>
Require all denied
</IfModule>
            ';

        return (bool) file_put_contents($pathToFile, $htaccessContent);

    }



    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger()
    {

        if (!$this->logger instanceof \TYPO3\CMS\Core\Log\Logger) {
            $this->logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Log\\LogManager')->getLogger(__CLASS__);
        }

        return $this->logger;
    }
}
