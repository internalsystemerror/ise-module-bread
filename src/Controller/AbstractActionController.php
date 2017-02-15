<?php

namespace Ise\Bread\Controller;

use Exception;
use Ise\Bread\Entity\EntityInterface;
use Ise\Bread\Router\Http\Bread;
use Ise\Bread\Service\ServiceInterface;
use Zend\Filter\Word\CamelCaseToSeparator;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController as ZendAbstractActionController;
use Zend\Stdlib\ResponseInterface;
use Zend\View\Model\ViewModel;

/**
 * @SuppressWarnings(PHPMD.ShortVariableName)
 */
abstract class AbstractActionController extends ZendAbstractActionController implements ActionControllerInterface
{
    
    /**
     * @var string
     */
    protected static $serviceClass;

    /**
     * @var string
     */
    protected static $indexRoute;

    /**
     * @var string
     */
    protected static $basePermission;

    /**
     * @var string
     */
    protected static $entityType;
    
    /**
     * @var ServiceInterface
     */
    protected $service;
    
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
    public function browseAction($viewTemplate = null)
    {
        // Check for access permission
        $this->checkPermission();
        
        // Create list view model
        return $this->createActionViewModel('browse', [
            'list' => $this->service->browse(),
        ], $viewTemplate);
    }

    /**
     * {@inheritDoc}
     */
    public function readAction($viewTemplate = null)
    {
        // Load entity
        $entity  = $this->getEntity();
        if (!$entity) {
            return $this->notFoundAction();
        }
        // Check for access permission
        $this->checkPermission(null, $entity);
        return $this->createActionViewModel('read', [
            'entity' => $entity
        ], $viewTemplate);
    }

    /**
     * {@inheritDoc}
     */
    public function addAction($viewTemplate = null)
    {
        // PRG wrapper
        $prg = $this->prg();
        if ($prg instanceof ResponseInterface) {
            return $prg;
        }
        
        // Check access
        $this->checkPermission(Bread::ACTION_CREATE);
        $action = $this->performAction(Bread::ACTION_CREATE, $prg);
        if ($action) {
            return $action;
        }
        
        // Setup form
        $form = $this->service->getForm(Bread::ACTION_CREATE);
        $this->setupFormForView($form);
        
        // Return view
        return $this->createActionViewModel(Bread::ACTION_CREATE, [
            'form' => $form,
        ], $viewTemplate);
    }

    /**
     * {@inheritDoc}
     */
    public function editAction($viewTemplate = null)
    {
        // PRG wrapper
        $prg = $this->prg();
        if ($prg instanceof ResponseInterface) {
            return $prg;
        }
        
        // Check access
        $entity = $this->getEntity();
        if (!$entity) {
            return $this->notFoundAction();
        }
        $this->checkPermission(Bread::ACTION_UPDATE, $entity);
        
        // Setup form
        $form = $this->service->getForm(Bread::ACTION_UPDATE);
        $form->bind($entity);
        
        // Perform action
        if ($prg) {
            $prg[Bread::IDENTIFIER] = $entity->getId();
        }
        $action = $this->performAction(Bread::ACTION_UPDATE, $prg);
        if ($action) {
            return $action;
        }
        
        // Return view
        $this->setupFormForView($form);
        return $this->createActionViewModel(Bread::ACTION_UPDATE, [
            'entity' => $entity,
            'form'   => $form,
        ], $viewTemplate);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAction($viewTemplate = null)
    {
        return $this->dialogueAction(Bread::ACTION_DELETE, $viewTemplate);
    }

    /**
     * {@inheritDoc}
     */
    public function enableAction($viewTemplate = null)
    {
        return $this->dialogueAction(Bread::ACTION_ENABLE, $viewTemplate);
    }

    /**
     * {@inheritDoc}
     */
    public function disableAction($viewTemplate = null)
    {
        return $this->dialogueAction(Bread::ACTION_DISABLE, $viewTemplate);
    }
    
    /**
     * Perform dialogue action
     * 
     * @param string $actionType
     * @return ResponseInterface|ViewModel
     */
    protected function dialogueAction($actionType, $viewTemplate = null)
    {
        // PRG wrapper
        $prg = $this->prg();
        if ($prg instanceof ResponseInterface) {
            return $prg;
        }
        
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
        $action = $this->performAction($actionType, $prg);
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
    protected function performAction($actionType, $prg)
    {
        if ($prg === false) {
            return null;
        }
        
        if ($this->service->$actionType($prg)) {
            // Create titles
            $camelFilter   = new CamelCaseToSeparator;
            $actionTitle   = strtolower($camelFilter->filter($actionType));
            $entityTitle   = strtolower($camelFilter->filter(static::$entityType));
            // Set success message
            $this->flashMessenger()->addSuccessMessage(sprintf(
                '%s %s successful.',
                ucfirst($actionType),
                $entityTitle
            ));
            return $this->redirect()->toRoute(static::$indexRoute);
        }
        return false;
    }
    
    /**
     * Create dialogue view model wrapper
     * 
     * @param string $actionType
     * @param Form $form
     * @param EntityInterface $entity
     * @param null|string $viewTemplate
     * @return ViewModel
     */
    protected function createDialogueViewModelWrapper($actionType, Form $form, EntityInterface $entity, $viewTemplate = null)
    {
        // Create titles
        $camelFilter   = new CamelCaseToSeparator;
        $actionTitle   = strtolower($camelFilter->filter($actionType));
        $entityTitle   = strtolower($camelFilter->filter(static::$entityType));
        
        // Create body
        $dialogueBody  = $this->createActionViewModel('dialogue', [
            'actionTitle' => $actionTitle,
            'entityTitle' => $entityTitle,
            'entity'      => $entity,
        ], $viewTemplate);
        
        // Create view model wrapper
        $viewModel = new ViewModel([
            'form'          => $form,
            'dialogueTitle' => sprintf('%s %s', ucwords($actionTitle), ucwords($entityTitle)),
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
        $entityTitle   = strtolower($camelFilter->filter(static::$entityType));
        
        // Set parametersstatic::$entityType
        $variables = array_merge([
            'basePermission' => static::$basePermission,
            'indexRoute'     => static::$indexRoute,
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
     * @return EntityInterface|boolean
     */
    protected function getEntity()
    {
        // Get entity id
        $id = (string) $this->params(Bread::IDENTIFIER, '');
        if (!$id) {
            return false;
        }
        
        $entity = $this->service->read($id);
        if (!$entity) {
            return false;
        }
        return $entity;
    }
    
    /**
     * Check for permission
     * 
     * @param string|null $actionType
     * @param mixed|null $context
     * @throws Exception
     */
    abstract protected function checkPermission($actionType = null, $context = null);
    
    /**
     * Setup form for view
     * 
     * @param Form $form
     */
    protected function setupFormForView(Form $form)
    {
        $form->setAttribute('class', 'form-horizontal');
        $form->get('buttons')->get('cancel')->setAttribute(
            'href',
            $this->url()->fromRoute(static::$indexRoute)
        );
    }
    
    /**
     * Setup form for dialogue
     * 
     * @param Form $form
     */
    protected function setupFormForDialogue(Form $form)
    {
        $form->get('buttons')->get('cancel')->setAttributes([
            'data-href'    => $this->url()->fromRoute(static::$indexRoute),
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
        return $this->redirect()->toRoute(static::$indexRoute);
    }
}
