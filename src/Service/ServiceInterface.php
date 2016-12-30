<?php

namespace Ise\Bread\Service;

/**
 * @SuppressWarnings(PHPMD.ShortVariableName)
 */
interface ServiceInterface
{

    /**
     * Browse entities
     *
     * @return object[]
     */
    public function browse($criteria = [], $orderBy = null, $limit = null, $offset = null);

    /**
     * Read entity
     *
     * @param  integer $id Entity id
     * @return object
     */
    public function read($id);

    /**
     * Add entity
     *
     * @param  array $data Entity data
     * @return boolean|object
     */
    public function add(array $data);

    /**
     * Edit entity
     *
     * @param  array $data Entity data
     * @return boolean|object
     */
    public function edit(array $data);

    /**
     * Delete entity
     *
     * @param  array $data Entity data
     * @return boolean|object
     */
    public function delete(array $data);

    /**
     * Disable entity
     *
     * @param  array $data Entity data
     * @return boolean|object
     */
    public function disable(array $data);

    /**
     * Enable entity
     *
     * @param  array $data Entity data
     * @return boolean|object
     */
    public function enable(array $data);
}
