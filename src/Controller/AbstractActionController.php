<?php

namespace Ise\Bread\Controller;

use Ise\Bread\Entity\AbstractEntity;
use Ise\Bread\Service\ServiceInterface;
use Zend\Mvc\Controller\AbstractActionController as ZendAbstractActionController;
use Zend\Stdlib\ResponseInterface;
use Zend\View\Model\ViewModel;
use ZfcRbac\Exception\UnauthorizedException;

/**
 * @SuppressWarnings(PHPMD.ShortVariableName)
 */
abstract class AbstractActionController extends ZendAbstractActionController implements ActionControllerInterface
{

    /**
     * @var ServiceInterface
     */
    protected $service;

    /**
     * @var string
     */
    protected $identifier = 'id';

    /**
     * @var string
     */
    protected $indexRoute = '';

    /**
     * @var string
     */
    protected $basePermission = '';

    /**
     * @var string
     */
    protected $entityType = '';

    /**
     * Constructor
     *
     * @param ServiceInterface $service
     */
    public function __construct(ServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * {@inheritDoc}
     */
    public function browseAction()
    {
        // Check for access permission
        if (!$this->isGranted($this->basePermission . '.browse')) {
            throw new UnauthorizedException();
        }
        return $this->createViewModel('browse', ['list' => $this->service->browse()]);
    }

    /**
     * {@inheritDoc}
     */
    public function readAction()
    {
        // Load entity
        $id      = (string) $this->params($this->identifier, 0);
        $service = $this->service;
        $entity  = $service->read($id);
        if (!$entity) {
            return $this->notFoundAction();
        }
        // Check for access permission
        if (!$this->isGranted($this->basePermission . '.read')) {
            throw new UnauthorizedException();
        }
        return $this->createViewModel('read', ['entity' => $entity]);
    }

    /**
     * {@inheritDoc}
     */
    public function addAction()
    {
        return $this->bread('add');
    }

    /**
     * {@inheritDoc}
     */
    public function editAction()
    {
        return $this->bread('edit');
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAction()
    {
        return $this->bread('delete');
    }

    /**
     * {@inheritDoc}
     */
    public function enableAction()
    {
        return $this->bread('enable');
    }

    /**
     * {@inheritDoc}
     */
    public function disableAction()
    {
        return $this->bread('disable');
    }

    /**
     * Perform bread dialogue action
     *
     * @param  string $actionType
     * @param  null|string $viewTemplate
     * @return ResponseInterface|ViewModel
     */
    protected function bread($actionType, $viewTemplate = null)
    {
        // Load entity
        $entity = $this->getEntity($actionType);
        if ($entity === false) {
            return $this->notFoundAction();
        }

        // Check for access permission
        if (!$this->isGranted($this->basePermission . '.' . $actionType)) {
            throw new UnauthorizedException();
        }

        // Load form
        $form = $this->service->getForm($actionType);
        if ($actionType !== 'add') {
            $form->bind($entity);
        }

        // Create PRG wrapper
        $prg = $this->prg();
        if ($prg instanceof ResponseInterface) {
            return $prg;
        } elseif ($prg !== false) {
            // Perform action
            if ($this->service->$actionType($prg)) {
                // Set success message
                $this->flashMessenger()->addSuccessMessage(
                    ucfirst($actionType) . ' ' . $this->entityType . ' "'
                    . $form->getData() . '" successful'
                );
                return $this->redirect()->toRoute($this->indexRoute);
            }
        }

        // Create view model
        return $this->createViewModel($actionType, [
            'entity' => $entity,
            'form'   => $form,
        ], $viewTemplate);
    }

    /**
     * Create view model
     *
     * @param string      $actionType
     * @param array       $parameters
     * @param string|null $viewTemplate
     * @return ViewModel
     */
    protected function createViewModel($actionType, array $parameters = [], $viewTemplate = null)
    {
        $defaults  = [
            'basePermission' => $this->basePermission,
            'indexRoute'     => $this->indexRoute,
            'entityType'     => $this->entityType,
        ];
        $viewModel = new ViewModel(array_merge($defaults, $parameters));
        if (!$viewTemplate) {
            $viewTemplate = 'ise-bread/bread/' . $actionType;
        }
        $viewModel->setTemplate($viewTemplate);
        return $viewModel;
    }

    /**
     * Get entity
     *
     * @param string $actionType
     * @return AbstractEntity|boolean|null
     */
    protected function getEntity($actionType)
    {
        $id = (string) $this->params($this->identifier, '');
        if ($id) {
            $entity = $this->service->read($id);
            if ($entity) {
                return $entity;
            }
        } elseif ($actionType === 'add') {
            return null;
        }
        return false;
    }
}
