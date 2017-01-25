<?php

namespace Ise\Bread\Mapper\DoctrineOrm;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Exception;
use Ise\Bread\Entity\EntityInterface;
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
     * @var EntityRepository
     */
    protected $entityRepository;
    
    /**
     * {@inheritDoc}
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager  = $entityManager;
        $this->entityReposity = $entityManager->getRepository(self::$entityClass);
    }

    /**
     * {@inheritDoc}
     */
    public function browse(array $criteria = [], array $orderBy = [], $limit = null, $offset = null)
    {
        
        return $this->entityReposity->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritDoc}
     */
    public function read($id)
    {
        return $this->entityReposity->find($id);
    }
    
    /**
     * {@inheritDoc}
     */
    public function readBy(array $criteria)
    {
        return $this->entityReposity->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function add(EntityInterface $entity)
    {
        return $this->persist($entity);
    }

    /**
     * {@inheritDoc}
     */
    public function edit(EntityInterface $entity)
    {
        return $this->persist($entity);
    }
    
    /**
     * {@inheritDoc}
     */
    public function delete(EntityInterface $entity)
    {
        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush($entity);
            return $entity;
        } catch (Exception $e) {
            if (APPLICATION_ENV === 'development') {
                throw $e;
            }
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function disable(EntityInterface $entity)
    {
        return $this->persist($entity);
    }

    /**
     * {@inheritDoc}
     */
    public function enable(EntityInterface $entity)
    {
        return $this->persist($entity);
    }

    /**
     * {@inheritDoc}
     */
    protected function persist(EntityInterface $entity)
    {
        try {
            $this->entityManager->persist($entity);
            $this->entityManager->flush($entity);
            return $entity;
        } catch (Exception $e) {
            if (APPLICATION_ENV === 'development') {
                throw $e;
            }
            return false;
        }
    }
}
