<?php

namespace Ise\Bread\Controller;

use Zend\Stdlib\ResponseInterface;
use Zend\View\Model\ViewModel;

interface ControllerInterface
{

    /**
     * Browse action
     *
     * @return ViewModel|ResponseInterface
     */
    public function browseAction();

    /**
     * Read action
     *
     * @return ViewModel|ResponseInterface
     */
    public function readAction();

    /**
     * Add action
     *
     * @return ViewModel|ResponseInterface
     */
    public function addAction();

    /**
     * Edit action
     *
     * @return ViewModel|ResponseInterface
     */
    public function editAction();

    /**
     * Delete action
     *
     * @return ViewModel|ResponseInterface
     */
    public function deleteAction();

    /**
     * Disable action
     *
     * @return ViewModel|ResponseInterface
     */
    public function disableAction();

    /**
     * Enable action
     *
     * @return ViewModel|ResponseInterface
     */
    public function enableAction();
}
