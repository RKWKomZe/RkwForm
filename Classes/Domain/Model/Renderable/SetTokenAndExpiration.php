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

/**
 * Class SetTokenAndExpiration
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SetTokenAndExpiration
{

    /**
     * function generateRandomSha1
     *
     * @return string
     */
    public function generateRandomSha1()
    {

        return sha1(rand());
        //====

    }

    /**
     * @param \TYPO3\CMS\Form\Domain\Runtime\FormRuntime $formRuntime
     * @param \TYPO3\CMS\Form\Domain\Model\Renderable\RenderableInterface $renderable
     * @param mixed $elementValue submitted value of the element "before post processing"
     * @param array $requestArguments submitted raw request values
     * @return void
     */
    public function afterSubmit(\TYPO3\CMS\Form\Domain\Runtime\FormRuntime $formRuntime, \TYPO3\CMS\Form\Domain\Model\Renderable\RenderableInterface $renderable, $elementValue, array $requestArguments = [])
    {
        $identifier = $renderable->getIdentifier();

        if ($identifier === 'token') {
            $elementValue = $this->generateRandomSha1();
        }

        if ($identifier === 'valid_until') {
            // token valid for seven days
            $hoursForOptIn = 24;
            $elementValue = strtotime("+" . $hoursForOptIn . " hour");
        }

        return $elementValue;
    }

}
