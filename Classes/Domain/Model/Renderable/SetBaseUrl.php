<?php
namespace RKW\RkwForm\Domain\Model\Renderable;

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

use TYPO3\CMS\Form\Domain\Model\Renderable\RenderableInterface;
use TYPO3\CMS\Form\Domain\Runtime\FormRuntime;

/**
 * Class SetBaseUrl
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SetBaseUrl
{
    /**
     * @param \TYPO3\CMS\Form\Domain\Runtime\FormRuntime $formRuntime
     * @param \TYPO3\CMS\Form\Domain\Model\Renderable\RenderableInterface $renderable
     * @param mixed $elementValue submitted value of the element "before post processing"
     * @param array $requestArguments submitted raw request values
     * @return  mixed
     *  @todo mixed return value is not optimal
     */
    public function afterSubmit(
        FormRuntime $formRuntime,
        RenderableInterface $renderable,
        $elementValue,
        array $requestArguments = []
    ) {

        $identifier = $renderable->getIdentifier();
        if ($identifier === 'confirmurl') {

            $verifypid = $requestArguments['verifypid'];
            $url = $GLOBALS['TSFE']->cObj->typolink_URL(
                [
                    'parameter' => $verifypid,
                    'forceAbsoluteUrl' => true,
                ]
            );
            $elementValue = $url;

        }

        return $elementValue;
    }

}
