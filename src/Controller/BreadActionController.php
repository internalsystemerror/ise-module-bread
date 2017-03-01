<?php

namespace Ise\Bread\Controller;

use Exception;
use Ise\Bread\Entity\EntityInterface;
use Ise\Bread\Options\ControllerOptions;
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
class BreadActionController extends ZendAbstractActionController implements ActionControllerInterface
{

    /**
     * @var ServiceInterface
     */
    protected $service;
    
    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var string
     */
    protected $indexRoute;

    /**
     * @var string
     */
    protected $basePermission;

    /**
     * @var string
     */
    protected $entityTitle;

    /**
     * @var array
     */
    protected $templates;

    /**
     * Constructor
     *
     * @param ServiceInterface $service
     */
    public function __construct(ServiceInterface $service, ControllerOptions $options)
    {
        $this->service        = $service;
        $this->entityClass    = $options->getEntityClass();
        $this->indexRoute     = $options->getIndexRoute();
        $this->basePermission = $options->getBasePermission();
        $this->entityTitle    = $options->getEntityTitle();
        $this->templates      = $options->getTemplates();
    }

    /**
     * {@inheritDoc}
     */
    public function browseAction()
    {
        // Check for access permission
        $this->checkPermission();

        // Create list view model
        return $this->createActionViewModel(Bread::ACTION_INDEX, [
            'list' => $this->service->browse(),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function readAction()
    {
        // Load entity
        $entity = $this->getEntity();
        if (!$entity) {
            return $this->notFoundAction();
        }
        // Check for access permission
        $this->checkPermission(null, $entity);
        return $this->createActionViewModel(Bread::ACTION_READ, [
            'entity' => $entity
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function addAction()
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
        $form = $this->service->getForm(Bread::FORM_CREATE);
        $this->setupFormForView($form);

        // Return view
        return $this->createActionViewModel(Bread::ACTION_CREATE, [
            'form' => $form,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function editAction()
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
        $form = $this->service->getForm(Bread::FORM_UPDATE);
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
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAction()
    {
        return $this->dialogAction(Bread::ACTION_DELETE);
    }

    /**
     * {@inheritDoc}
     */
    public function enableAction()
    {
        return $this->dialogAction(Bread::ACTION_ENABLE);
    }

    /**
     * {@inheritDoc}
     */
    public function disableAction()
    {
        return $this->dialogAction(Bread::ACTION_DISABLE);
    }

    /**
     * Perform dialog action
     *
     * @param string $actionType
     * @return ResponseInterface|ViewModel
     */
    protected function dialogAction($actionType)
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
        $notAllowed = $this->checkDialogueNotAllowed($actionType, $entity);
        if ($notAllowed) {
            return $notAllowed;
        }

        // Setup form
        $form = $this->service->getForm(Bread::FORM_DIALOG);
        $form->bind($entity);

        // Perform action
        if ($prg) {
            $prg[Bread::IDENTIFIER] = $entity->getId();
        }
        $action = $this->performAction($actionType, $prg);
        if ($action) {
            return $action;
        }

        // Return view
        $this->setupFormForDialogue($form);
        return $this->createDialogueViewModelWrapper($actionType, $form, $entity);
    }

    /**
     * Check if dialog is not allowed
     *
     * @param string $actionType
     * @param EntityInterface $entity
     * @return null|ReponseInterface
     */
    protected function checkDialogueNotAllowed($actionType, EntityInterface $entity)
    {
        switch ($actionType) {
            case Bread::ACTION_DISABLE:
                if (!$entity->isDisabled()) {
                    return;
                }
                // Set warning message
                $this->flashMessenger()->addWarningMessage(sprintf(
                    'That %s is already disabled',
                    $this->entityTitle
                ));
                return $this->redirect()->toRoute($this->indexRoute);
            case Bread::ACTION_ENABLE:
                if ($entity->isDisabled()) {
                    return;
                }
                // Set warning message
                $this->flashMessenger()->addWarningMessage(sprintf(
                    'That %s is already enabled',
                    $this->entityTitle
                ));
                return $this->redirect()->toRoute($this->indexRoute);
        }
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
            $camelFilter = new CamelCaseToSeparator;
            $actionTitle = strtolower($camelFilter->filter($actionType));
            // Set success message
            $this->flashMessenger()->addSuccessMessage(sprintf(
                '%s %s successful.',
                ucfirst($actionType),
                $this->entityTitle
            ));
            return $this->redirect()->toRoute($this->indexRoute);
        }
        return false;
    }

    /**
     * Create dialog view model wrapper
     *
     * @param string $actionType
     * @param Form $form
     * @param EntityInterface $entity
     * @param null|string $viewTemplate
     * @return ViewModel
     */
    protected function createDialogueViewModelWrapper($actionType, Form $form, EntityInterface $entity)
    {
        // Create titles
        $camelFilter = new CamelCaseToSeparator;
        $actionTitle = strtolower($camelFilter->filter($actionType));

        // Create body
        $dialogBody = $this->createActionViewModel(Bread::FORM_DIALOG, [
            'actionTitle' => $actionTitle,
            'entityTitle' => $this->entityTitle,
            'entity'      => $entity,
        ]);

        // Create view model wrapper
        $viewModel = new ViewModel([
            'form'        => $form,
            'dialogTitle' => sprintf('%s %s', ucwords($actionTitle), ucwords($this->entityTitle)),
        ]);
        $viewModel->setTemplate('partial/dialog');
        $viewModel->addChild($dialogBody, 'dialogBody');

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
    protected function createActionViewModel($actionType, array $parameters = [])
    {
        // Set parameters$this->entityTitle
        $variables = array_merge([
            'basePermission' => $this->basePermission,
            'indexRoute'     => $this->indexRoute,
            'entityTitle'    => ucwords($this->entityTitle),
            ], $parameters);

        // Set up view model
        $viewModel = new ViewModel($variables);
        if (isset($this->templates[$actionType])) {
            $viewModel->setTemplate($this->templates[$actionType]);
        }
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
    protected function checkPermission($actionType = null, $context = null)
    {
    }

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
            $this->url()->fromRoute($this->indexRoute)
        );
    }

    /**
     * Setup form for dialog
     *
     * @param Form $form
     */
    protected function setupFormForDialogue(Form $form)
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
