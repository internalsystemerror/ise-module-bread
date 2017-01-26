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
     * @var string
     */
    protected static $entityClass;
    
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
        $this->entityRepository = $entityManager->getRepository(static::$entityClass);
    }

    /**
     * {@inheritDoc}
     */
    public function browse(array $criteria = [], array $orderBy = [], $limit = null, $offset = null)
    {
        
        return $this->entityRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritDoc}
     */
    public function read($id)
    {
        return $this->entityRepository->find($id);
    }
    
    /**
     * {@inheritDoc}
     */
    public function readBy(array $criteria)
    {
        return $this->entityRepository->findOneBy($criteria);
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
        $entity->setLastModified(new DateTime);
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
        $entity->setDisabled(true);
        $entity->setLastModified(new DateTime);
        return $this->persist($entity);
    }

    /**
     * {@inheritDoc}
     */
    public function enable(EntityInterface $entity)
    {
        $entity->setDisabled(false);
        $entity->setLastModified(new DateTime);
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
