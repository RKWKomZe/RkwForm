<?php

namespace RKW\RkwForm\Validation\Validator;

use Madj2k\CoreExtended\Utility\GeneralUtility;
use RKW\RkwBasics\Helper\Common;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

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
 * Class GemCommunityFormValidator
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class GemCommunityFormValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
{

    /**
     * validation
     *
     * @param  \RKW\RkwForm\Domain\Model\GemCommunityForm $gemCommunityForm
     * @return boolean
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function isValid($gemCommunityForm): bool
    {
        // initialize typoscript settings
        $settings = $this->getSettings();

        // get mandatory fields
        $mandatoryFieldsStandard = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(",", $settings['mandatoryFields']['gemCommunity']);

        $isValid = true;

        // 1. Check on valid email
        if (method_exists($gemCommunityForm, 'getEmail')) {
            $value = trim($gemCommunityForm->getEmail());
            if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->result->forProperty('email')->addError(
                    new \TYPO3\CMS\Extbase\Error\Error(
                        \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                            'form.error.invalidEmail',
                            'rkw_form',
                            [$value]
                        ), 1566822699
                    )
                );
                $isValid = false;
            }
        }

        // 2. Check mandatory fields
        if ($mandatoryFieldsStandard) {

            foreach ($mandatoryFieldsStandard as $field) {

                $getter = 'get' . ucfirst($field);

                if (method_exists($gemCommunityForm, $getter)) {

                    if (
                        (
                            ($field == 'salutation')
                            && (trim($gemCommunityForm->$getter()) == 99)
                        )
                        ||
                        (
                            ($field != 'salutation')
                            && (!trim($gemCommunityForm->$getter()))
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
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected static function getSettings(string $which = ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS): array
    {
        return GeneralUtility::getTypoScriptConfiguration('Rkwform', $which);
    }

}
