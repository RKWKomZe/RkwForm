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

use TYPO3\CMS\Form\Domain\Model\Renderable\RootRenderableInterface;
use TYPO3\CMS\Form\Domain\Runtime\FormRuntime;

/**
 * Class SetDynamicDefaultValue
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SetDynamicDefaultValue
{
    /**
     * @param \TYPO3\CMS\Form\Domain\Runtime\FormRuntime                      $formRuntime
     * @param \TYPO3\CMS\Form\Domain\Model\Renderable\RootRenderableInterface $renderable
     * @return void
     */
    public function beforeRendering(FormRuntime $formRuntime, RootRenderableInterface $renderable)
    {
        if (
            method_exists($renderable, 'getDefaultValue')
            &&
            strpos($renderable->getDefaultValue(), 'formValues["') !== false
        ) {
            $defaultFormValue = explode('"', $renderable->getDefaultValue());
            $renderable->setDefaultValue($formRuntime->getFormState()->getFormValue($defaultFormValue[1]));
        }
    }

}
