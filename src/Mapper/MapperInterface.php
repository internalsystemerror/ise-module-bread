<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Mapper;

use Ise\Bread\Entity\EntityInterface;

/**
 * @SuppressWarnings(PHPMD.ShortVariableName)
 */
interface MapperInterface
{

    /**
     * Browse entities
     *
     * @param  array    $criteria
     * @param  array    $orderBy
     * @param  null|int $limit
     * @param  null|int $offset
     *
     * @return EntityInterface[]
     */
    public function browse(array $criteria = [], array $orderBy = [], int $limit = null, int $offset = null): array;

    /**
     * Read entity
     *
     * @param  string $id
     *
     * @return EntityInterface|null
     */
    public function read(string $id): ?EntityInterface;

    /**
     * Read an entity by criteria
     *
     * @param array $criteria
     *
     * @return EntityInterface|null
     */
    public function readBy(array $criteria): ?EntityInterface;

    /**
     * Add entity
     *
     * @param  EntityInterface $entity
     *
     * @return void
     * @throws \Exception
     */
    public function add(EntityInterface $entity): void;

    /**
     * Add many entities
     *
     * @param iterable $entities
     *
     * @return void
     * @throws \Exception
     */
    public function addMany(iterable $entities): void;

    /**
     * Edit entity
     *
     * @param  EntityInterface $entity
     *
     * @return void
     * @throws \Exception
     */
    public function edit(EntityInterface $entity): void;

    /**
     * Edit many entities
     *
     * @param iterable $entities
     *
     * @return void
     * @throws \Exception
     */
    public function editMany(iterable $entities): void;

    /**
     * Delete entity
     *
     * @param  EntityInterface $entity
     *
     * @return void
     * @throws \Exception
     */
    public function delete(EntityInterface $entity): void;

    /**
     * Delete many entities
     *
     * @param iterable $entities
     *
     * @return void
     * @throws \Exception
     */
    public function deleteMany(iterable $entities): void;

    /**
     * Begin transaction
     *
     * @return void
     */
    public function beginTransaction(): void;

    /**
     * Commit transaction
     *
     * @return void
     */
    public function commit(): void;

    /**
     * Rollback transaction
     *
     * @return void
     */
    public function rollback(): void;
}
