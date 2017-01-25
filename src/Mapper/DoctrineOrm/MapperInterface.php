<?php

namespace Ise\Bread\Mapper\DoctrineOrm;

use Doctrine\ORM\EntityManager;
use Ise\Bread\Mapper\MapperInterface as IseMapperInterface;

interface MapperInterface extends IseMapperInterface
{

    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager);
}
