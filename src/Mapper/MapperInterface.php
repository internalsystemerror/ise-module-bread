<?php

namespace Ise\Bread\Mapper;

/**
 * @SuppressWarnings(PHPMD.ShortVariableName)
 */
interface MapperInterface
{

    /**
     * Browse entities
     *
     * @return array
     */
    public function browse();

    /**
     * Read entity
     *
     * @param  integer $id
     * @return object
     */
    public function read($id);

    /**
     * Add entity
     *
     * @param  object $entity
     * @return boolean|object
     */
    public function add($entity);

    /**
     * Edit entity
     *
     * @param  object $entity
     * @return boolean|object
     */
    public function edit($entity);

    /**
     * Delete entity
     *
     * @param  object $entity
     * @return boolean
     */
    public function delete($entity);

    /**
     * Disable entity
     *
     * @param  object $entity
     * @return boolean
     */
    public function disable($entity);

    /**
     * Enable entity
     *
     * @param  object $entity
     * @return boolean
     */
    public function enable($entity);
}
