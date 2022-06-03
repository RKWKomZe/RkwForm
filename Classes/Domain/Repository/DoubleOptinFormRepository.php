<?php
namespace RKW\RkwForm\Domain\Repository;

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
 * Class DoubleOptinFormRepository
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwForm
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class DoubleOptinFormRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * find form entries by token
     *
     * @param string $token
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findByToken(string $token = '')
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        $constraints = [];

        $constraints[] = $query->equals('token', $token);
        $constraints[] = $query->greaterThanOrEqual('validUntil', time());

        // NOW: construct final query!
        $query->matching($query->logicalAnd($constraints));

        return $query->execute()->getFirst();
        //====
    }

}
