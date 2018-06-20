<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Service;

use Ise\Bread\Entity\EntityInterface;
use Zend\Form\FormInterface;

/**
 * @SuppressWarnings(PHPMD.ShortVariableName)
 */
interface ServiceInterface
{

    /**
     * Browse entities
     *
     * @param array    $criteria
     * @param array    $orderBy
     * @param null|int $limit
     * @param null|int $offset
     *
     * @return array
     */
    public function browse(array $criteria = [], array $orderBy = [], $limit = null, $offset = null): array;

    /**
     * Read an entity
     *
     * @param  string $id Entity id
     *
     * @return EntityInterface|null
     */
    public function read($id): ?EntityInterface;

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
     * @param  array $data Entity data
     *
     * @return EntityInterface|null
     */
    public function add(array $data): ?EntityInterface;

    /**
     * Edit entity
     *
     * @param  array $data Entity data
     *
     * @return EntityInterface|null
     */
    public function edit(array $data): ?EntityInterface;

    /**
     * Delete entity
     *
     * @param  array $data Entity data
     *
     * @return EntityInterface|null
     */
    public function delete(array $data): ?EntityInterface;

    /**
     * Disable entity
     *
     * @param  array $data Entity data
     *
     * @return EntityInterface|null
     */
    public function disable(array $data): ?EntityInterface;

    /**
     * Enable entity
     *
     * @param  array $data Entity data
     *
     * @return EntityInterface|null
     */
    public function enable(array $data): ?EntityInterface;

    /**
     * Get a form
     *
     * @param string $form
     *
     * @return FormInterface
     */
    public function getForm(string $form): FormInterface;
}
