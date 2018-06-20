<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Controller;

interface ControllerInterface
{

    /**
     * Browse action
     *
     * @return mixed
     */
    public function browseAction();

    /**
     * Read action
     *
     * @return mixed
     */
    public function readAction();

    /**
     * Add action
     *
     * @return mixed
     */
    public function addAction();

    /**
     * Edit action
     *
     * @return mixed
     */
    public function editAction();

    /**
     * Delete action
     *
     * @return mixed
     */
    public function deleteAction();

    /**
     * Disable action
     *
     * @return mixed
     */
    public function disableAction();

    /**
     * Enable action
     *
     * @return mixed
     */
    public function enableAction();
}
