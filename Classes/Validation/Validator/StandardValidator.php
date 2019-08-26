<?php
namespace RKW\RkwForm\Validation\Validator;

use \RKW\RkwBasics\Helper\Common;
use \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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
 * Class StandardValidator
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class StandardValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
{

    /**
     * validation
     *
     * @var \RKW\RkwForm\Domain\Model\Standard $newStandard
     * @return boolean
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function isValid($newStandard)
    {
        // initialize typoscript settings
        $settings = $this->getSettings();

        // get mandatory fields
        $mandatoryFieldsStandard = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(",", $settings['mandatoryFields']['standard']);

        $isValid = true;

        // 1. Check mandatory fields main person
        if ($mandatoryFieldsStandard) {

            foreach ($mandatoryFieldsStandard as $field) {

                $getter = 'get' . ucfirst($field);
                if (method_exists($newStandard, $getter)) {

                    if (
                        (
                            ($field == 'salutation')
                            && (trim($newStandard->$getter()) == 99)
                        )
                        ||
                        (
                            ($field != 'salutation')
                            && (!trim($newStandard->$getter()))
                        )
                    ) {

                        $propertyName = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                            'form.error.newFormRequest.' . lcfirst($field),
                            'rkw_form'
                        );

                        $this->result->forProperty(lcfirst($field))->addError(
                            new \TYPO3\CMS\Extbase\Error\Error(
                                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                                    'form.error.notFilled',
                                    'rkw_form',
                                    array($propertyName)
                                ), 1566822699
                            )
                        );
                        $isValid = false;
                    }
                }
            }
        }

        return $isValid;
        //===
    }


    /**
     * Returns TYPO3 settings
     *
     * @param string $which Which type of settings will be loaded
     * @return array
     */
    protected static function getSettings($which = ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS)
    {
        return Common::getTyposcriptConfiguration('Rkwform', $which);
        //===
    }

}

