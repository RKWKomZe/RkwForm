<?php
namespace RKW\RkwForm\Controller;

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

use RKW\RkwForm\Domain\Model\StandardForm;

/**
 * Class StandardFormController
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class StandardFormController  extends \RKW\RkwForm\Controller\AbstractFormController
{
    /**
     * action create
     *
     * @param \RKW\RkwForm\Domain\Model\StandardForm $standardForm
     * @param bool $privacy
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @TYPO3\CMS\Extbase\Annotation\Validate("RKW\RkwForm\Validation\Validator\AbstractFormValidator", param="standardForm")
     */
    public function createAction(StandardForm $standardForm, bool $privacy = false): void
    {
        parent::createAbstractAction($standardForm, $privacy);
    }
}
