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
 * Class GetValueFromArrayViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class GetValueFromArrayViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{

    use CompileWithRenderStatic;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('array', 'array', 'The array to get data from', true);
        $this->registerArgument('value', 'string', 'The array-key to find', true);
    }


    /**
     * needed for dynamic created variables which contains variables
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

        $array = $arguments['array'];
        $value = $arguments['value'];

        if(array_key_exists($value, $array)) {
            return $array[$value];
        }

        return '';
    }

}
