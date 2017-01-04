<?php

namespace Ise\Bread\Controller;

use Ise\Bread\Entity\AbstractEntity;
use Ise\Bread\Router\Http\BreadRouteStack;
use Ise\Bread\Service\ServiceInterface;
use Zend\Filter\Word\CamelCaseToSeparator;
use Zend\Form\Form;
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
     * @var string
     */
    protected static $serviceClass = '';
    
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
     * Get service class
     *
     * @return string
     */
    public static function getServiceClass()
    {
        return static::$serviceClass;
    }

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
        $this->checkPermission();
        
        // Create list view model
        return $this->createActionViewModel('browse', ['list' => $this->service->browse()]);
    }

    /**
     * {@inheritDoc}
     */
    public function readAction()
    {
        // Load entity
        $entity  = $this->getEntity();
        if (!$entity) {
            return $this->notFoundAction();
        }
        // Check for access permission
        $this->checkPermission(null, $entity);
        return $this->createActionViewModel('read', ['entity' => $entity]);
    }

    /**
     * {@inheritDoc}
     */
    public function addAction()
    {
        // Check access
        $this->checkPermission(BreadRouteStack::ACTION_CREATE);
        $action = $this->performAction(BreadRouteStack::ACTION_CREATE);
        if ($action) {
            return $action;
        }
        
        // Setup form
        $form = $this->service->getForm(BreadRouteStack::ACTION_CREATE);
        $this->setupFormForView($form);
        
        // Return view
        return $this->createActionViewModel(BreadRouteStack::ACTION_CREATE, ['form' => $form,]);
    }

    /**
     * {@inheritDoc}
     */
    public function editAction()
    {
        // Check access
        $entity = $this->getEntity();
        if (!$entity) {
            return $this->notFoundAction();
        }
        $this->checkPermission(BreadRouteStack::ACTION_UPDATE, $entity);
        
        // Setup form
        $form = $this->service->getForm(BreadRouteStack::ACTION_UPDATE);
        $form->bind($entity);
        
        // Perform action
        $action = $this->performAction(BreadRouteStack::ACTION_UPDATE);
        if ($action) {
            return $action;
        }
        
        // Return view
        $this->setupFormForView($form);
        return $this->createActionViewModel(BreadRouteStack::ACTION_UPDATE, [
            'entity' => $entity,
            'form'   => $form,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAction()
    {
        return $this->dialogueAction(BreadRouteStack::ACTION_DELETE);
    }

    /**
     * {@inheritDoc}
     */
    public function enableAction()
    {
        return $this->dialogueAction(BreadRouteStack::ACTION_ENABLE);
    }

    /**
     * {@inheritDoc}
     */
    public function disableAction()
    {
        return $this->dialogueAction(BreadRouteStack::ACTION_DISABLE);
    }
    
    /**
     * Perform dialogue action
     * 
     * @param string $actionType
     * @return ResponseInterface|ViewModel
     */
    protected function dialogueAction($actionType, $viewTemplate = null)
    {
        // Check access
        $entity = $this->getEntity();
        if (!$entity) {
            return $this->notFoundAction();
        }
        $this->checkPermission($actionType, $entity);
        
        // Setup form
        $form = $this->service->getForm($actionType);
        $form->bind($entity);
        
        // Perform action
        $action = $this->performAction($actionType);
        if ($action) {
            return $action;
        }
        
        // Return view
        $this->setupFormForDialogue($form);
        return $this->createDialogueViewModelWrapper($actionType, $form, $entity, $viewTemplate);
    }

    /**
     * Perform action
     *
     * @param  string $actionType
     * @return ResponseInterface|null
     */
    protected function performAction($actionType)
    {
        // Create PRG wrapper
        $prg = $this->prg();
        if ($prg instanceof ResponseInterface) {
            return $prg;
        } elseif ($prg !== false) {
            // Perform action
            if ($this->service->$actionType($prg)) {
                // Set success message
                $this->flashMessenger()->addSuccessMessage(
                    ucfirst($actionType) . ' ' . $this->entityType . ' successful.'
                );
                return $this->redirect()->toRoute($this->indexRoute);
            }
            return false;
        }
        return null;
    }
    
    /**
     * Create dialogue view model wrapper
     * 
     * @param string $actionType
     * @param Form $form
     * @param AbstractEntity $entity
     * @param null|string $viewTemplate
     * @return ViewModel
     */
    protected function createDialogueViewModelWrapper($actionType, Form $form, AbstractEntity $entity, $viewTemplate = null)
    {
        // Create titles
        $camelFilter   = new CamelCaseToSeparator;
        $actionTitle   = strtolower($camelFilter->filter($actionType));
        $entityTitle   = strtolower($camelFilter->filter($this->entityType));
        
        // Create body
        $dialogueBody  = $this->createActionViewModel('dialogue', [
            'actionTitle' => $actionTitle,
            'entityTitle' => $entityTitle,
            'entity'      => $entity,
        ], $viewTemplate);
        
        // Create view model wrapper
        $viewModel = new ViewModel([
            'form'          => $form,
            'dialogueTitle' => ucwords($actionTitle . ' ' . $entityTitle),
        ]);
        $viewModel->setTemplate('partial/dialogue');
        $viewModel->addChild($dialogueBody, 'dialogueBody');
        
        return $viewModel;
    }

    /**
     * Create view model
     *
     * @param string      $actionTemplate
     * @param array       $parameters
     * @param string|null $viewTemplate
     * @return ViewModel
     */
    protected function createActionViewModel($actionTemplate, array $parameters = [], $viewTemplate = null)
    {
        // Create title
        $camelFilter   = new CamelCaseToSeparator;
        $entityTitle   = strtolower($camelFilter->filter($this->entityType));
        
        // Set parameters$this->entityType
        $variables = array_merge([
            'basePermission' => $this->basePermission,
            'indexRoute'     => $this->indexRoute,
            'entityTitle'    => ucwords($entityTitle),
        ], $parameters);
        
        // Set up view model
        $viewModel = new ViewModel($variables);
        if (!$viewTemplate) {
            $viewTemplate = 'ise/bread/bread/' . $actionTemplate;
        }
        $viewModel->setTemplate($viewTemplate);
        return $viewModel;
    }

    /**
     * Get entity
     *
     * @return AbstractEntity|boolean
     */
    protected function getEntity()
    {
        $id = (string) $this->params($this->identifier, '');
        if ($id) {
            $entity = $this->service->read($id);
            if ($entity) {
                return $entity;
            }
        }
        return false;
    }
    
    /**
     * Check for permission
     * 
     * @param string|null $actionType
     * @param mixed|null $context
     * @throws UnauthorizedException
     */
    protected function checkPermission($actionType = null, $context = null)
    {
        $permission = $this->basePermission;
        if ($actionType) {
            $permission .= '.' . $actionType;
        }
        if (!$this->isGranted($permission, $context)) {
            throw new UnauthorizedException;
        }
    }
    
    /**
     * Setup form for view
     * 
     * @param Form $form
     */
    protected function setupFormForView($form)
    {
        $form->setAttribute('action', $this->url()->fromRoute(null, [], null, true));
        $form->setAttribute('class', 'form-horizontal');
        $form->get('buttons')->get('cancel')->setAttribute(
            'href',
            $this->url()->fromRoute($this->indexRoute)
        );
    }
    
    /**
     * Setup form for dialogue
     * 
     * @param Form $form
     */
    protected function setupFormForDialogue($form)
    {
        $form->get('buttons')->get('cancel')->setAttributes([
            'data-href'    => $this->url()->fromRoute($this->indexRoute),
            'data-dismiss' => 'modal',
        ]);
    }
    
    /**
     * Redirect browse to index route
     * 
     * @return ResponseInterface
     */
    protected function redirectBrowse()
    {
        // Check for access permission
        $this->checkPermission();
        
        // Redirect to index route
        return $this->redirect()->toRoute($this->indexRoute);
    }
}
