<?php

namespace Ise\Bread\Mapper\DoctrineOrm;

use Doctrine\ORM\EntityManager;
use Ise\Bread\Mapper\AbstractMapper as IseAbstractMapper;

/**
 * @SuppressWarnings(PHPMD.ShortVariableName)
 */
abstract class AbstractMapper extends IseAbstractMapper implements MapperInterface
{
    
    /**
     * @var EntityManager
     */
    protected $entityManager;
    
    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Browse entities
     *
     * @return array
     */
    public function browse($criteria = [], $orderBy = null, $limit = null, $offset = null)
    {
        return $this->entityManager->getRepository($this->entityClass)->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Read entity
     *
     * @param  integer $id
     * @return object
     */
    public function read($id)
    {
        return $this->entityManager->getRepository($this->entityClass)->find($id);
    }

    /**
     * Add entity
     *
     * @param  object $entity
     * @return boolean|object
     */
    public function add($entity)
    {
        return $this->persist($entity);
    }

    /**
     * Edit entity
     *
     * @param  object $entity
     * @return boolean|object
     */
    public function edit($entity)
    {
        return $this->persist($entity);
    }

    /**
     * Delete entity
     *
     * @param  object $entity
     * @return boolean
     */
    public function delete($entity)
    {
        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush($entity);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Disable entity
     *
     * @param  object $entity
     * @return boolean|object
     */
    public function disable($entity)
    {
        return $this->persist($entity);
    }

    /**
     * Enable entity
     *
     * @param  object $entity
     * @return boolean|object
     */
    public function enable($entity)
    {
        return $this->persist($entity);
    }

    /**
     * Persist entity
     *
     * @param  object $entity
     * @return boolean|object
     */
    protected function persist($entity)
    {
        try {
            $this->entityManager->persist($entity);
            $this->entityManager->flush($entity);
            return $entity;
        } catch (\Exception $e) {
            if (APPLICATION_ENV === 'development') {
                throw $e;
            }
            return false;
        }
    }
}
