<?php

namespace RKW\RkwForm\ViewHelpers;
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

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;

/**
 * Class GetFinisherOptionViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class GetFinisherOptionViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * resolves problem of protected finisher options. Get them via YamlFileLoader!
     *
     * @param string $formIdentifier The form identifier
     * @param string $finisherIdentifier The identifier of the finisher
     * @param string $getOption The option which should read
     * @return string
     */
    public function render($formIdentifier, $finisherIdentifier, $getOption)
    {
        /** @var \TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader $yamlFileLoader */
        $yamlFileLoader = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Configuration\\Loader\\YamlFileLoader');
        $formConfiguration = $yamlFileLoader->load('EXT:rkw_form/Configuration/Yaml/Forms/' . $formIdentifier . '.form.yaml');

        foreach ($formConfiguration['finishers'] as $finisher) {
            if ($finisher['identifier'] == $finisherIdentifier) {
                if (array_key_exists($getOption, $finisher['options'])) {
                    // ATTENTION: If the value is a variable, the "{}" signs will be removed!
                    return trim($finisher['options'][$getOption], '{}');
                }
            }
        }

        return '';
    }


}