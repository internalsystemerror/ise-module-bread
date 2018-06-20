<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Mapper\DoctrineOrm;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ise\Bread\Mapper\MapperInterface as IseMapperInterface;

interface MapperInterface extends IseMapperInterface
{

    /**
     * Constructor
     *
     * @param EntityManager    $entityManager
     * @param EntityRepository $entityRepository
     */
    public function __construct(EntityManager $entityManager, EntityRepository $entityRepository);
}
