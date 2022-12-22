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
 * Class GemCommunityForm
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class GemCommunityForm extends \RKW\RkwForm\Domain\Model\StandardForm
{

    /**
     * token
     *
     * @var string
     */
    protected $token = '';

    /**
     * validUntil
     *
     * @var int
     */
    protected $validUntil = 0;

    /**
     * identifier
     *
     * @var string
     */
    protected $identifier = '';

    /**
     * street
     *
     * @var string
     */
    protected $street = '';

    /**
     * postal
     *
     * @var string
     */
    protected $postal = '';

    /**
     * city
     *
     * @var string
     */
    protected $city = '';

    /**
     * theme
     *
     * @var string
     */
    protected $theme = '';

    /**
     * verificationUrl
     *
     * @var string
     */
    protected $verificationUrl = '';

    /**
     * enabled
     *
     * @var bool
     */
    protected $enabled = 0;

    /**
     * Returns the type
     *
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type
     *
     * @param string $type
     * @return void
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Returns the identifier
     *
     * @return string $identifier
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Sets the identifier
     *
     * @param string $identifier
     * @return void
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * Returns the street
     *
     * @return string $street
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Sets the street
     *
     * @param string $street
     * @return void
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * Returns the postal
     *
     * @return string $postal
     */
    public function getPostal()
    {
        return $this->postal;
    }

    /**
     * Sets the postal
     *
     * @param string $postal
     * @return void
     */
    public function setPostal($postal)
    {
        $this->postal = $postal;
    }

    /**
     * Returns the city
     *
     * @return string $city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Sets the city
     *
     * @param string $city
     * @return void
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Returns the theme
     *
     * @return string $theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Sets the theme
     *
     * @param string $theme
     * @return void
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    /**
     * Returns the verificationUrl
     *
     * @return string $verificationUrl
     */
    public function getVerificationUrl()
    {
        return $this->verificationUrl;
    }

    /**
     * Sets the verificationUrl
     *
     * @param string $verificationUrl
     * @return void
     */
    public function setVerificationUrl(string $verificationUrl): void
    {
        $this->verificationUrl = $verificationUrl;
    }

    /**
     * Returns the token
     *
     * @return string $token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets the token
     *
     * @param string $token
     * @return void
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * Returns the validUntil
     *
     * @return int $validUntil
     */
    public function getValidUntil()
    {
        return $this->validUntil;
    }

    /**
     * Sets the validUntil
     *
     * @param int $validUntil
     * @return void
     */
    public function setValidUntil(int $validUntil): void
    {
        $this->validUntil = $validUntil;
    }

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }


}
