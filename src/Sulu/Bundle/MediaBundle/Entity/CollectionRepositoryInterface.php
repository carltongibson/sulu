<?php

/*
 * This file is part of the Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\MediaBundle\Entity;

use Sulu\Component\Security\Authentication\UserInterface;

/**
 * Defines the method for the doctrine repository.
 */
interface CollectionRepositoryInterface
{
    /**
     * Finds a collection set starting by given ID and depth.
     *
     * @param int $depth
     * @param array $filter
     * @param CollectionInterface $collection
     * @param array $sortBy
     * @param UserInterface $user The user for which the additional access control should be checked
     * @param int $permission The permission mask the user requires, if it is passed for the access control check
     *
     * @return Collection[]
     */
    public function findCollectionSet(
        $depth = 0,
        $filter = [],
        CollectionInterface $collection = null,
        $sortBy = [],
        UserInterface $user = null,
        $permission = null
    );

    /**
     * Finds the collection with a given id.
     *
     * @param int $id
     *
     * @return Collection
     */
    public function findCollectionById($id);

    /**
     * finds all collections, can be filtered with parent and depth.
     *
     * @param array $filter
     * @param int $limit
     * @param int $offset
     * @param array $sortBy sort by e.g. array('title' => 'ASC')
     *
     * @return Collection[]
     */
    public function findCollections($filter = [], $limit = null, $offset = null, $sortBy = []);

    /**
     * Finds the breadcrumb of a collection with given id.
     *
     * @param int $id
     *
     * @return Collection[]
     */
    public function findCollectionBreadcrumbById($id);
}
