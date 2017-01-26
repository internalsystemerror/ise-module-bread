<?php

namespace Ise\Bread\Service;

use Ise\Bread\Entity\EntityInterface;

/**
 * @SuppressWarnings(PHPMD.ShortVariableName)
 */
interface ServiceInterface
{

    /**
     * Browse entities
     * 
     * @param array $criteria
     * @param array $orderBy
     * @param null|integer $limit
     * @param null|integer $offset
     */
    public function browse(array $criteria = [], array $orderBy = [], $limit = null, $offset = null);

    /**
     * Read an entity
     *
     * @param  integer $id Entity id
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
     * @param  array $data Entity data
     * @return boolean|EntityInterface
     */
    public function add(array $data);

    /**
     * Edit entity
     *
     * @param  array $data Entity data
     * @return boolean|EntityInterface
     */
    public function edit(array $data);

    /**
     * Delete entity
     *
     * @param  array $data Entity data
     * @return boolean|EntityInterface
     */
    public function delete(array $data);

    /**
     * Disable entity
     *
     * @param  array $data Entity data
     * @return boolean|EntityInterface
     */
    public function disable(array $data);

    /**
     * Enable entity
     *
     * @param  array $data Entity data
     * @return boolean|EntityInterface
     */
    public function enable(array $data);
}
