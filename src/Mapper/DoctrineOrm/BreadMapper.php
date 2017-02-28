<?php

namespace Ise\Bread\Mapper\DoctrineOrm;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Exception;
use Ise\Bread\Entity\EntityInterface;
use Traversable;

/**
 * @SuppressWarnings(PHPMD.ShortVariableName)
 */
class BreadMapper implements MapperInterface
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
     * @var Connection 
     */
    protected $connection;
    
    /**
     * {@inheritDoc}
     */
    public function __construct(EntityManager $entityManager, EntityRepository $entityRepository)
    {
        $this->entityManager    = $entityManager;
        $this->entityRepository = $entityRepository;
        $this->connection       = $entityManager->getConnection();
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
    public function addMany(Traversable $entities)
    {
        return $this->persistMany($entities);
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
    public function editMany(Traversable $entities)
    {
        return $this->persistMany($entities);
    }
    
    /**
     * {@inheritDoc}
     */
    public function delete(EntityInterface $entity)
    {
        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
            $this->entityManager->clear();
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
    public function deleteMany(Traversable $entities)
    {
        try {
            foreach ($entities as $entity) {
                $this->entityManager->remove($entity);
            }
            $this->entityManager->flush();
            $this->entityManager->clear();
            return $entities;
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
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * {@inheritDoc}
     */
    public function commit()
    {
        return $this->connection->commit();
    }

    /**
     * {@inheritDoc}
     */
    public function rollback()
    {
        return $this->connection->rollback();
    }

    /**
     * Persist entity
     * 
     * @param EntityInterface $entity
     * @return EntityInterface|boolean
     * @throws Exception
     */
    protected function persist(EntityInterface $entity)
    {
        try {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            $this->entityManager->clear();
            return $entity;
        } catch (Exception $e) {
            if (APPLICATION_ENV === 'development') {
                throw $e;
            }
            return false;
        }
    }
    
    /**
     * Persist many entities
     * 
     * @param EntityInterface[] $entities
     * @return EntityInterface[]|boolean
     */
    protected function persistMany(Traversable $entities)
    {
        try {
            foreach ($entities as $entity) {
                $this->entityManager->persist($entity);
            }
            $this->entityManager->flush();
            $this->entityManager->clear();
            return $entities;
        } catch (Exception $e) {
            if (APPLICATION_ENV === 'development') {
                throw $e;
            }
            return false;
        }
    }
}
