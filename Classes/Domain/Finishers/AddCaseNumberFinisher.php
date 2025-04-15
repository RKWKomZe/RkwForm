<?php
namespace RKW\RkwForm\Domain\Finishers;

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

use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;

/**
 * Class SendToUrl
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class AddCaseNumberFinisher extends AbstractFinisher
{

    /**
     * Executes this finisher
     * @see AbstractFinisher::execute()
     */
    protected function executeInternal(): string
    {

        $this->finisherContext->getFormRuntime()->offsetSet('caseNumber', '#' . strtoupper(uniqid()));

        return '';
    }


}
