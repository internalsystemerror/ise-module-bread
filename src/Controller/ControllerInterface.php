<?php

namespace Ise\Bread\Controller;

use Zend\View\Model\ViewModel;

interface ControllerInterface
{

    /**
     * Browse action
     *
     * @return ViewModel
     */
    public function browseAction();

    /**
     * Read action
     *
     * @return ViewModel
     */
    public function readAction();

    /**
     * Add action
     *
     * @return ViewModel
     */
    public function addAction();

    /**
     * Edit action
     *
     * @return ViewModel
     */
    public function editAction();

    /**
     * Delete action
     *
     * @return ViewModel
     */
    public function deleteAction();

    /**
     * Disable action
     *
     * @return ViewModel
     */
    public function disableAction();

    /**
     * Enable action
     *
     * @return ViewModel
     */
    public function enableAction();
}
