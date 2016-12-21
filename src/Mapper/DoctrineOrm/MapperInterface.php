<?php

namespace IseBread\Mapper\DoctrineOrm;

use IseBread\Mapper\MapperInterface as IseMapperInterface;

/**
 * @SuppressWarnings(PHPMD.ShortVariableName)
 */
interface MapperInterface extends IseMapperInterface
{

    /**
     * Browse entities
     *
     * @param  array        $criteria
     * @param  array|null   $orderBy
     * @param  integer|null $limit
     * @param  integer|null $offset
     * @return array
     */
    public function browse($criteria = [], $orderBy = null, $limit = null, $offset = null);
}
