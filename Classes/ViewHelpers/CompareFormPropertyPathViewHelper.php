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

/**
 * Class IsMandatoryFieldViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CompareFormPropertyPathViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * return TRUE, if the given propertyPath matches the elementIdentifier
     * TRUE if optional
     *
     * @param string $propertyPath
     * @param string $elementIdentifier
     * @return bool
     */
    public function render($propertyPath, $elementIdentifier)
    {
        // {propertyPath} --- eepa.{element.identifier}

        if (str_ends_with($propertyPath, $elementIdentifier)) {
            return true;
        }
        return false;
    }


}