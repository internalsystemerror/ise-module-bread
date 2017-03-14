<?php

namespace Ise\Bread\Mapper;

use Ise\Bread\Entity\EntityInterface;
use Traversable;

/**
 * @SuppressWarnings(PHPMD.ShortVariableName)
 */
interface MapperInterface
{

    /**
     * Browse entities
     *
     * @param  array        $criteria
     * @param  array        $orderBy
     * @param  null|integer $limit
     * @param  null|integer $offset
     * @return EntityInterface[]
     */
    public function browse(array $criteria = [], array $orderBy = [], $limit = null, $offset = null);

    /**
     * Read entity
     *
     * @param  integer $id
     * @return EntityInterface
     */
    public function read($id);

    /**
     * Read an entity by criteria
     *
     * @param array $criteria
     * @return EntityInterface
     */
    public function readBy(array $criteria);

    /**
     * Add entity
     *
     * @param  EntityInterface $entity
     * @return boolean|EntityInterface
     */
    public function add(EntityInterface $entity);
    
    /**
     * Add many entities
     *
     * @param EntityInterface[] $entities
     * @return boolean|EntityInterface[]
     */
    public function addMany(Traversable $entities);

    /**
     * Edit entity
     *
     * @param  EntityInterface $entity
     * @return boolean|EntityInterface
     */
    public function edit(EntityInterface $entity);
    
    /**
     * Edit many entities
     *
     * @param EntityInterface[] $entities
     * @return boolean|EntityInterface[]
     */
    public function editMany(Traversable $entities);

    /**
     * Delete entity
     *
     * @param  EntityInterface $entity
     * @return boolean|EntityInterface
     */
    public function delete(EntityInterface $entity);
    
    /**
     * Delete many entities
     *
     * @param Traversable $entities
     */
    public function deleteMany(Traversable $entities);
    
    /**
     * Begin transaction
     */
    public function beginTransaction();
    
    /**
     * Commit transaction
     */
    public function commit();
    
    /**
     * Rollback transaction
     */
    public function rollback();
}
