<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Mapper\DoctrineOrm;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Ise\Bread\Entity\EntityInterface;

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
     * @inheritdoc
     */
    public function __construct(EntityManager $entityManager, EntityRepository $entityRepository)
    {
        $this->entityManager    = $entityManager;
        $this->entityRepository = $entityRepository;
        $this->connection       = $entityManager->getConnection();
    }

    /**
     * @inheritdoc
     */
    public function browse(array $criteria = [], array $orderBy = [], int $limit = null, int $offset = null): array
    {
        return $this->entityRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function read(string $id): ?EntityInterface
    {
        return $this->validateEntity($this->entityRepository->find($id));
    }

    /**
     * @inheritdoc
     */
    public function readBy(array $criteria): ?EntityInterface
    {
        return $this->validateEntity($this->entityRepository->findOneBy($criteria));
    }

    /**
     * @inheritdoc
     */
    public function add(EntityInterface $entity): void
    {
        $this->persist($entity);
    }

    /**
     * @inheritdoc
     */
    public function addMany(iterable $entities): void
    {
        $this->persistMany($entities);
    }

    /**
     * @inheritdoc
     */
    public function edit(EntityInterface $entity): void
    {
        $this->persist($entity);
    }

    /**
     * @inheritdoc
     */
    public function editMany(iterable $entities): void
    {
        $this->persistMany($entities);
    }

    /**
     * @inheritdoc
     */
    public function delete(EntityInterface $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function deleteMany(iterable $entities): void
    {
        foreach ($entities as $entity) {
            $this->entityManager->remove($entity);
        }
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    /**
     * @inheritdoc
     * @throws ConnectionException
     */
    public function commit(): void
    {
        $this->connection->commit();
    }

    /**
     * @inheritdoc
     * @throws ConnectionException
     */
    public function rollback(): void
    {
        $this->connection->rollback();
    }

    /**
     * Persist entity
     *
     * @param EntityInterface $entity
     *
     * @return void
     * @throws ORMException
     */
    protected function persist(EntityInterface $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * Persist many entities
     *
     * @param iterable $entities
     *
     * @return void
     * @throws ORMException
     */
    protected function persistMany(iterable $entities): void
    {
        foreach ($entities as $entity) {
            $this->entityManager->persist($entity);
        }
        $this->entityManager->flush();
    }

    /**
     * Validate entity
     *
     * @param EntityInterface|null $entity
     *
     * @return EntityInterface|null
     */
    private function validateEntity(EntityInterface $entity = null)
    {
        return $entity;
    }
}
