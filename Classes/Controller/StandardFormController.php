<?php
namespace RKW\RkwForm\Controller;

use \RKW\RkwForm\Domain\Model\StandardForm;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Messaging\AbstractMessage;

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
 * Class StandardFormController
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class StandardFormController  extends \RKW\RkwForm\Controller\AbstractFormController
{
    /**
     * action create
     *
     * @param \RKW\RkwForm\Domain\Model\StandardForm $standardForm
     * @param int $privacy
     * @TYPO3\CMS\Extbase\Annotation\Validate("\RKW\RkwForm\Validation\Validator\AbstractFormValidator", param="standardForm")
     * @return void
     */
    public function createAction(StandardForm $standardForm, $privacy = 0)
    {
        parent::createAbstractAction($standardForm, $privacy);
    }
}
