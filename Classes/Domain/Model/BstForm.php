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
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BstForm extends \RKW\RkwForm\Domain\Model\StandardForm
{
    /**
     * @var int
     */
    protected int $bstNumber1 = 0;


    /**
     * @var int
     */
    protected int $bstNumber2 = 0;


    /**
     * @var int
     */
    protected int $bstNumber3 = 0;


    /**
     * @var bool
     */
    protected bool $bstAgree = false;


    /**
     * @return int
     */
    public function getBstNumber1(): int
    {
        return $this->bstNumber1;
    }

    /**
     * @param int $bstNumber1
     * @return void
     */
    public function setBstNumber1(int $bstNumber1): void
    {
        $this->bstNumber1 = $bstNumber1;
    }


    /**
     * @return int
     */
    public function getBstNumber2(): int
    {
        return $this->bstNumber2;
    }


    /**
     * @param int $bstNumber2
     * @return void
     */
    public function setBstNumber2(int $bstNumber2): void
    {
        $this->bstNumber2 = $bstNumber2;
    }


    /**
     * @return int
     */
    public function getBstNumber3(): int
    {
        return $this->bstNumber3;
    }


    /**
     * @param int $bstNumber3
     * @return void
     */
    public function setBstNumber3(int $bstNumber3): void
    {
        $this->bstNumber3 = $bstNumber3;
    }

    /**
     * @return bool
     */
    public function getBstAgree(): bool
    {
        return $this->bstAgree;
    }


    /**
     * @param bool $bstAgree
     * @return void
     */
    public function setBstAgree(bool $bstAgree): void
    {
        $this->bstAgree = $bstAgree;
    }
}
