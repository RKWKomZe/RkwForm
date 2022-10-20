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

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Class IsMandatoryFieldViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class IsMandatoryFieldViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{


    use CompileWithRenderStatic;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('fieldName', 'string', 'The name of the field that is to be checked against mandatoryFields.', true);
        $this->registerArgument('mandatoryFields', 'string', 'Comma-separated list of field-names that are mandatory.', true);
    }


    /**
     * return TRUE, if the given fieldName is NOT in given mandatoryFields (string-list from TypoScript)
     * TRUE if optional
     *
     * @param array $arguments
     * @param \Closure  $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        $fieldName = $arguments['fieldName'];
        $mandatoryFields = $arguments['mandatoryFields'];
        $mandatoryFieldsArray = array_map('trim', explode(',', $mandatoryFields));

        if (!in_array($fieldName, $mandatoryFieldsArray)) {
            return true;
        }

        return false;
    }


}
