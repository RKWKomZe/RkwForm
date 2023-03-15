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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Driver\PDOSqlsrv\Connection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\EndTimeRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\StartTimeRestriction;
use TYPO3\CMS\Form\Domain\Model\Renderable\RenderableInterface;

/**
 * Class GetPostParameter
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class GetPostParameter
{

    /**
     * @var \TYPO3\CMS\Core\Database\Connection|null
     */
    protected ?Connection $databaseConnection = null;


    /**
     * @param \TYPO3\CMS\Form\Domain\Model\Renderable\RenderableInterface $renderable
     * @return void
     */
    public function afterBuildingFinished(RenderableInterface $renderable): void
    {
        $identifier = $renderable->getIdentifier();
        $GPvariable = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('token');

        if ($GPvariable && $identifier === 'gettoken') {

            $this->databaseConnection = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable('tx_rkwform_domain_model_standardform');

            // find go through all pages
            /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
            $queryBuilder = $this->databaseConnection->createQueryBuilder();
            $statement = $queryBuilder->select('*')
                ->from('tx_rkwform_domain_model_standardform')
                ->where(
                    $queryBuilder->expr()->eq('token',
                        $queryBuilder->createNamedParameter($GPvariable, \PDO::PARAM_STR)
                    )
                )
                ->execute();

            $renderable->setDefaultValue($GPvariable);

        }
    }

}
