<?php
namespace RKW\RkwForm\Domain\Model;

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
 * Class BstForm
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BstForm extends \RKW\RkwForm\Domain\Model\StandardForm
{
    /**
     * bstNumber1
     *
     * @var int
     */
    protected $bstNumber1 = 0;

    /**
     * bstNumber2
     *
     * @var int
     */
    protected $bstNumber2 = 0;

    /**
     * bstNumber3
     *
     * @var int
     */
    protected $bstNumber3 = 0;

    /**
     * bstAgree
     *
     * @var bool
     */
    protected $bstAgree = 0;

    /**
     * @return int
     */
    public function getBstNumber1()
    {
        return $this->bstNumber1;
    }

    /**
     * @param int $bstNumber1
     */
    public function setBstNumber1($bstNumber1)
    {
        $this->bstNumber1 = $bstNumber1;
    }

    /**
     * @return int
     */
    public function getBstNumber2()
    {
        return $this->bstNumber2;
    }

    /**
     * @param int $bstNumber2
     */
    public function setBstNumber2($bstNumber2)
    {
        $this->bstNumber2 = $bstNumber2;
    }

    /**
     * @return int
     */
    public function getBstNumber3()
    {
        return $this->bstNumber3;
    }

    /**
     * @param int $bstNumber3
     */
    public function setBstNumber3($bstNumber3)
    {
        $this->bstNumber3 = $bstNumber3;
    }

    /**
     * @return bool
     */
    public function getBstAgree()
    {
        return $this->bstAgree;
    }

    /**
     * @param bool $bstAgree
     */
    public function setBstAgree($bstAgree)
    {
        $this->bstAgree = $bstAgree;
    }
}
