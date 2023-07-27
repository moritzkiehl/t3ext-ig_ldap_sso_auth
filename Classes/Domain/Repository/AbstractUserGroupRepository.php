<?php
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

namespace Causal\IgLdapSsoAuth\Domain\Repository;

use TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Abstract class AbstractUserGroupRepository when willing to work
 * with Extbase-based domain objects for Backend and Frontend user
 * groups before Extbase is even ready and only basic operations
 * are needed (no mapping nor fancy other stuff).
 *
 * @author     Xavier Perseguers <xavier@causal.ch>
 * @package    TYPO3
 * @subpackage ig_ldap_sso_auth
 */
abstract class AbstractUserGroupRepository
{
    /**
     * @var string
     */
    protected $className = '';

    /**
     * @var string
     */
    protected $tableName = '';

    /**
     * @var string
     */
    protected $selectFields = 'uid, pid, title, hidden';

    /**
     * Default constructor enforcing properties has been defined in derived classes.
     */
    final public function __construct()
    {
        if (empty($this->className)) {
            throw new \LogicException(static::class . ' must have a property $className', 1_449_144_226);
        }
        if (empty($this->tableName)) {
            throw new \LogicException(static::class . ' must have a property $tableName', 1_449_144_585);
        }
    }

    /**
     * Returns a single backend/frontend user group.
     *
     * @return BackendUserGroup|FrontendUserGroup|null
     */
    public function findByUid(int $uid)
    {
        $row = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($this->tableName)
            ->select(
                GeneralUtility::trimExplode(',', $this->selectFields, true),
            $this->tableName,
                [
                    'uid' => (int)$uid,
                ]
            )
            ->fetchAssociative();

        if (!empty($row)) {
            $userGroup = GeneralUtility::makeInstance($this->className);
            $this->thawProperties($userGroup, $row);
        } else {
            $userGroup = null;
        }

        return $userGroup;
    }

    /**
     * Sets the given properties on the object.
     *
     * @param AbstractEntity $object The object to set properties on
     * @return $this
     */
    protected function thawProperties(AbstractEntity $object, array $row): self
    {
        foreach ($row as $field => $value) {
            $propertyName = GeneralUtility::underscoredToLowerCamelCase($field);
            $object->_setProperty($propertyName, $value);
        }
        return $this;
    }
}
