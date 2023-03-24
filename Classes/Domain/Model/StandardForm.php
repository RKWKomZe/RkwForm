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

use Madj2k\FeRegister\Domain\Model\Title;

/**
 * Class StandardForm
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class StandardForm extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * @var int
     */
    protected int $salutation = 99;


    /**
     * @var \Madj2k\FeRegister\Domain\Model\Title|null
     */
    protected ?Title $title = null;


    /**
     * @var string
     */
    protected string $firstName = '';


    /**
     * @var string
     */
    protected string $lastName = '';


    /**
     * @var string
     */
    protected string $company = '';


    /**
     * @var string
     */
    protected string $email = '';


    /**
     * @var string
     */
    protected string $phone = '';


    /**
     * @var string
     */
    protected string $text = '';


    /**
     * @return int $salutation
     */
    public function getSalutation(): int
    {
        return $this->salutation;
    }


    /**
     * @param int $salutation
     * @return void
     */
    public function setSalutation(int $salutation): void
    {
        $this->salutation = $salutation;
    }


    /**
     * Returns the title
     *
     * @return \Madj2k\FeRegister\Domain\Model\Title|null $title
     */
    public function getTitle():? Title
    {
        return $this->title;
    }


    /**
     * @param \Madj2k\FeRegister\Domain\Model\Title|null $title
     * @return void
     */
    public function setTitle(Title $title = null): void
    {
        $this->title = $title;
    }


    /**
     * Returns the firstName
     *
     * @return string $firstName
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }


    /**
     * Sets the firstName
     *
     * @param string $firstName
     * @return void
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }


    /**
     * Returns the lastName
     *
     * @return string $lastName
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }


    /**
     * Sets the lastName
     *
     * @param string $lastName
     * @return void
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }


    /**
     * Returns the company
     *
     * @return string $company
     */
    public function getCompany(): string
    {
        return $this->company;
    }


    /**
     * Sets the company
     *
     * @param string $company
     * @return void
     */
    public function setCompany(string $company): void
    {
        $this->company = $company;
    }


    /**
     * Returns the email
     *
     * @return string $email
     */
    public function getEmail(): string
    {
        return $this->email;
    }


    /**
     * Sets the email
     *
     * @param string $email
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }


    /**
     * Returns the phone
     *
     * @return string $phone
     */
    public function getPhone(): string
    {
        return $this->phone;
    }


    /**
     * Sets the phone
     *
     * @param string $phone
     * @return void
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }


    /**
     * Returns the text
     *
     * @return string $text
     */
    public function getText(): string
    {
        return $this->text;
    }


    /**
     * Sets the text
     *
     * @param string $text
     * @return void
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }
}
