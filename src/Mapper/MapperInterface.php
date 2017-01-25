<?php

namespace Ise\Bread\Mapper;

use Ise\Bread\Entity\EntityInterface;

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
     * Edit entity
     *
     * @param  EntityInterface $entity
     * @return boolean|EntityInterface
     */
    public function edit(EntityInterface $entity);

    /**
     * Delete entity
     *
     * @param  EntityInterface $entity
     * @return boolean|EntityInterface
     */
    public function delete(EntityInterface $entity);

    /**
     * Disable entity
     *
     * @param  EntityInterface $entity
     * @return boolean|EntityInterface
     */
    public function disable(EntityInterface $entity);

    /**
     * Enable entity
     *
     * @param  EntityInterface $entity
     * @return boolean|EntityInterface
     */
    public function enable(EntityInterface $entity);
}
