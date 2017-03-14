<?php

namespace Ise\Bread\Controller;

use Ise\Bread\Entity\EntityInterface;
use Ise\Bread\EventManager\BreadEvent;
use Ise\Bread\EventManager\BreadEventManager;
use Ise\Bread\Options\ControllerOptions;
use Ise\Bread\Service\ServiceInterface;
use Zend\Filter\Word\CamelCaseToSeparator;
use Zend\Mvc\Controller\AbstractActionController as ZendAbstractActionController;
use Zend\Http\Request;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\View\Model\ViewModel;

/**
 * @SuppressWarnings(PHPMD.ShortVariableName)
 */
class BreadActionController extends ZendAbstractActionController implements ActionControllerInterface
{

    /**
     * @var BreadEventManager
     */
    protected $breadEventManager;

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
    public function __construct(BreadEventManager $breadEventManager, ServiceInterface $service, ControllerOptions $options)
    {
        $this->breadEventManager = $breadEventManager;
        $this->service           = $service;
        $this->entityClass       = $options->getEntityClass();
        $this->indexRoute        = $options->getIndexRoute();
        $this->entityTitle       = $options->getEntityTitle();
        $this->templates         = $options->getTemplates();
        $this->attachDefaultBreadListeners();
    }

    /**
     * {@inheritDoc}
     */
    public function browseAction()
    {
        return $this->triggerActionEvent(BreadEvent::EVENT_INDEX, BreadEvent::ACTION_INDEX);
    }

    /**
     * {@inheritDoc}
     */
    public function readAction()
    {
        return $this->triggerActionEvent(BreadEvent::EVENT_READ, BreadEvent::ACTION_READ);
    }

    /**
     * {@inheritDoc}
     */
    public function addAction()
    {
        return $this->triggerActionEvent(BreadEvent::EVENT_CREATE, BreadEvent::ACTION_CREATE, BreadEvent::FORM_CREATE);
    }

    /**
     * {@inheritDoc}
     */
    public function editAction()
    {
        return $this->triggerActionEvent(BreadEvent::EVENT_UPDATE, BreadEvent::ACTION_UPDATE, BreadEvent::FORM_UPDATE);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAction()
    {
        return $this->triggerActionEvent(BreadEvent::EVENT_DIALOG, BreadEvent::ACTION_DELETE, BreadEvent::FORM_DIALOG);
    }

    /**
     * {@inheritDoc}
     */
    public function enableAction()
    {
        return $this->triggerActionEvent(BreadEvent::EVENT_DIALOG, BreadEvent::ACTION_ENABLE, BreadEvent::FORM_DIALOG);
    }

    /**
     * {@inheritDoc}
     */
    public function disableAction()
    {
        return $this->triggerActionEvent(BreadEvent::EVENT_DIALOG, BreadEvent::ACTION_DISABLE, BreadEvent::FORM_DIALOG);
    }

    /**
     * Browse action event
     *
     * @param BreadEvent $event
     */
    public function onActionBrowse(BreadEvent $event)
    {
        $viewModel = $event->getViewModel();
        $viewModel->setVariable('list', $this->service->browse());

        return $viewModel;
    }

    /**
     * Read action event
     *
     * @param BreadEvent $event
     */
    public function onActionRead(BreadEvent $event)
    {
        $viewModel = $event->getViewModel();
        $viewModel->setVariable('entity', $event->getEntity());

        return $viewModel;
    }

    /**
     * Create action event
     *
     * @param BreadEvent $event
     */
    public function onActionCreate(BreadEvent $event)
    {
        $viewModel = $event->getViewModel();
        $viewModel->setVariable('form', $event->getForm());

        return $viewModel;
    }

    /**
     * Update action event
     *
     * @param BreadEvent $event
     */
    public function onActionUpdate(BreadEvent $event)
    {
        $viewModel = $event->getViewModel();
        $viewModel->setVariables([
            'entity' => $event->getEntity(),
            'form'   => $event->getForm(),
        ]);

        return $viewModel;
    }

    /**
     * Perform dialog action
     *
     * @param BreadEvent $bread
     * @return ResponseInterface|ViewModel
     */
    public function onActionDialog(BreadEvent $event)
    {
        $camelFilter = new CamelCaseToSeparator;
        $viewModel   = $event->getViewModel();
        $viewModel->setVariables([
            'actionTitle' => strtolower($camelFilter->filter($event->getAction())),
            'entity'      => $event->getEntity(),
        ]);

        return $viewModel;
    }

    /**
     * Load PRG
     *
     * @param BreadEvent $event
     * @return null|ResponseInterface
     */
    public function loadPrg(BreadEvent $event)
    {
        $prg = $this->getPrgDataFromRequest();
        if ($prg instanceof ResponseInterface) {
            return $prg;
        }

        $event->setPrgData($prg);
    }

    /**
     * Load entity
     *
     * @param BreadEvent $event
     * @return type
     */
    public function loadEntity(BreadEvent $event)
    {
        // Load entity
        $entity = $this->getEntity();
        if (!$entity) {
            return $this->notFoundAction();
        }

        $event->setEntity($entity);
    }

    /**
     * Load form
     *
     * @param BreadEvent $event
     */
    public function loadForm(BreadEvent $event)
    {
        $event->setForm($this->service->getForm($event->getForm()));
    }

    /**
     * Bind entity to form
     *
     * @param BreadEvent $event
     */
    public function bindEntityToForm(BreadEvent $event)
    {
        $event->getForm()->bind($event->getEntity());
    }

    /**
     * POST data to service
     *
     * @return type
     */
    public function postToService(BreadEvent $event)
    {
        $action = $event->getAction();
        $data   = $event->getPrgData();
        if (!$data) {
            return;
        }

        // Trigger service action
        $result = $this->service->$action($data);
        if (!$result) {
            return;
        }

        // Create titles
        $camelFilter = new CamelCaseToSeparator;
        $actionTitle = strtolower($camelFilter->filter($action));
        // Set success message
        $this->flashMessenger()->addSuccessMessage(sprintf(
            '%s %s successful.',
            ucfirst($actionTitle),
            strtolower($this->entityTitle)
        ));
        return $this->redirect()->toRoute($this->indexRoute);
    }

    /**
     * Setup form for view
     *
     * @param BreadEvent $event
     */
    public function setupFormForView(BreadEvent $event)
    {
        $form = $event->getForm();
        $form->setAttribute('class', 'form-horizontal');
        $form->get('buttons')->get('cancel')->setAttribute(
            'href',
            $this->url()->fromRoute($this->indexRoute)
        );
    }

    /**
     * Setup form for dialog
     *
     * @param BreadEvent $event
     */
    public function setupFormForDialog(BreadEvent $event)
    {
        $form = $event->getForm();
        $form->get('buttons')->get('cancel')->setAttributes([
            'data-href'    => $this->url()->fromRoute($this->indexRoute),
            'data-dismiss' => 'modal',
        ]);
    }

    /**
     * Check if dialog is not allowed
     *
     * @param BreadEvent $event
     * @return null|ReponseInterface
     */
    public function checkDialogNotAllowed(BreadEvent $event)
    {
        switch ($event->getAction()) {
            case BreadEvent::ACTION_DISABLE:
                if (!$event->getEntity()->isDisabled()) {
                    return;
                }
                $this->flashMessenger()->addWarningMessage(sprintf(
                    'That %s is already disabled',
                    $this->entityTitle
                ));
                return $this->redirect()->toRoute($this->indexRoute);
            case BreadEvent::ACTION_ENABLE:
                if ($event->getEntity()->isDisabled()) {
                    return;
                }
                $this->flashMessenger()->addWarningMessage(sprintf(
                    'That %s is already enabled',
                    $this->entityTitle
                ));
                return $this->redirect()->toRoute($this->indexRoute);
        }
    }

    /**
     * Setup view model
     *
     * @param BreadEvent $event
     */
    public function setupViewModel(BreadEvent $event)
    {
        $viewModel = $event->getViewModel();
        $viewModel->setVariables([
            'basePermission' => ($this->basePermission),
            'indexRoute'     => ($this->indexRoute),
            'entityTitle'    => ucwords($this->entityTitle),
        ]);

        // Set up view model
        if (isset($this->templates[$event->getName()])) {
            $viewModel->setTemplate($this->templates[$event->getName()]);
        }

        return $viewModel;
    }

    /**
     * Wrap dialog view model
     *
     * @param BreadEvent $event
     * @return ViewModel
     */
    public function wrapDialogViewModel(BreadEvent $event)
    {
        // Create view model wrapper
        $dialogBody = $event->getViewModel();
        $viewModel  = new ViewModel([
            'form'        => $event->getForm(),
            'dialogTitle' => ucwords(sprintf(
                '%s %s',
                $dialogBody->getVariable('actionTitle'),
                $this->entityTitle
            )),
        ]);
        $viewModel->setTemplate('partial/dialog');
        $viewModel->addChild($dialogBody, 'dialogBody');
        $event->setViewModel($viewModel);

        return $viewModel;
    }

    /**
     * Attach default bread listeners
     */
    protected function attachDefaultBreadListeners()
    {
        $this->attachDefaultIndexListeners();
        $this->attachDefaultReadListeners();
        $this->attachDefaultCreateListeners();
        $this->attachDefaultUpdateListeners();
        $this->attachDefaultDialogListeners();
    }

    /**
     * Attach default index listeners
     */
    protected function attachDefaultIndexListeners()
    {
        $this->breadEventManager->attach(BreadEvent::EVENT_INDEX, [$this, 'onActionBrowse']);
        $this->breadEventManager->attach(BreadEvent::EVENT_INDEX, [$this, 'setupViewModel'], -100);
    }

    /**
     * Attach default read listeners
     */
    protected function attachDefaultReadListeners()
    {
        $this->breadEventManager->attach(BreadEvent::EVENT_READ, [$this, 'loadEntity'], 900);
        $this->breadEventManager->attach(BreadEvent::EVENT_READ, [$this, 'onActionRead']);
        $this->breadEventManager->attach(BreadEvent::EVENT_READ, [$this, 'setupViewModel'], -100);
    }

    /**
     * Attach default create listeners
     */
    protected function attachDefaultCreateListeners()
    {
        $this->breadEventManager->attach(BreadEvent::EVENT_CREATE, [$this, 'loadPrg'], 1000);
        $this->breadEventManager->attach(BreadEvent::EVENT_CREATE, [$this, 'postToService'], 500);
        $this->breadEventManager->attach(BreadEvent::EVENT_CREATE, [$this, 'loadForm'], 20);
        $this->breadEventManager->attach(BreadEvent::EVENT_CREATE, [$this, 'setupFormForView'], 10);
        $this->breadEventManager->attach(BreadEvent::EVENT_CREATE, [$this, 'onActionCreate']);
        $this->breadEventManager->attach(BreadEvent::EVENT_CREATE, [$this, 'setupViewModel'], -100);
    }

    /**
     * Attach default update listeners
     */
    protected function attachDefaultUpdateListeners()
    {
        $this->breadEventManager->attach(BreadEvent::EVENT_UPDATE, [$this, 'loadPrg'], 1000);
        $this->breadEventManager->attach(BreadEvent::EVENT_UPDATE, [$this, 'loadEntity'], 900);
        $this->breadEventManager->attach(BreadEvent::EVENT_UPDATE, [$this, 'loadForm'], 600);
        $this->breadEventManager->attach(BreadEvent::EVENT_UPDATE, [$this, 'bindEntityToForm'], 550);
        $this->breadEventManager->attach(BreadEvent::EVENT_UPDATE, [$this, 'postToService'], 500);
        $this->breadEventManager->attach(BreadEvent::EVENT_UPDATE, [$this, 'setupFormForView'], 10);
        $this->breadEventManager->attach(BreadEvent::EVENT_UPDATE, [$this, 'onActionUpdate']);
        $this->breadEventManager->attach(BreadEvent::EVENT_UPDATE, [$this, 'setupViewModel'], -100);
    }

    /**
     * Attach default dialog listeners
     */
    protected function attachDefaultDialogListeners()
    {
        $this->breadEventManager->attach(BreadEvent::EVENT_DIALOG, [$this, 'loadPrg'], 1000);
        $this->breadEventManager->attach(BreadEvent::EVENT_DIALOG, [$this, 'loadEntity'], 900);
        $this->breadEventManager->attach(BreadEvent::EVENT_DIALOG, [$this, 'checkDialogNotAllowed'], 800);
        $this->breadEventManager->attach(BreadEvent::EVENT_DIALOG, [$this, 'loadForm'], 600);
        $this->breadEventManager->attach(BreadEvent::EVENT_DIALOG, [$this, 'bindEntityToForm'], 550);
        $this->breadEventManager->attach(BreadEvent::EVENT_DIALOG, [$this, 'postToService'], 500);
        $this->breadEventManager->attach(BreadEvent::EVENT_DIALOG, [$this, 'setupFormForDialog'], 10);
        $this->breadEventManager->attach(BreadEvent::EVENT_DIALOG, [$this, 'onActionDialog']);
        $this->breadEventManager->attach(BreadEvent::EVENT_DIALOG, [$this, 'setupViewModel'], -100);
        $this->breadEventManager->attach(BreadEvent::EVENT_DIALOG, [$this, 'wrapDialogViewModel'], -200);
    }

    /**
     * Trigger a bread action event
     *
     * @param string $name
     * @param null|string $action
     * @param null|string $form
     * @return ViewModel|ReponseInterface
     */
    protected function triggerActionEvent($name, $action = null, $form = null)
    {
        if (!$action) {
            $action = $name;
        }

        // Setup new event
        $event = new BreadEvent;
        $event->setName($name);
        $event->setAction($action);
        $event->setForm($form);
        $event->setViewModel(new ViewModel);

        // Get result from action
        $result = $this->breadEventManager->triggerEventUntil(function ($test) {
            if ($test instanceof ResponseInterface) {
                return true;
            }

            $mvcEvent   = $this->getEvent();
            $routeMatch = $mvcEvent->getRouteMatch();
            $actionName = $routeMatch->getParam('action');
            if ($actionName === 'not-found') {
                return true;
            }

            return false;
        }, $event);

        if ($result->stopped()) {
            return $result->last();
        }

        return $result->last();
    }

    /**
     * Get entity
     *
     * @return EntityInterface|boolean
     */
    protected function getEntity()
    {
        // Get entity id
        $id = (string) $this->params(BreadEvent::IDENTIFIER, '');
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
     * Redirect browse to index route
     *
     * @return ResponseInterface
     */
    protected function redirectBrowse()
    {
        // Redirect to index route
        return $this->redirect()->toRoute($this->indexRoute);
    }

    /**
     * Get PRG data from request
     *
     * @return boolean|array|ResponseInterface
     */
    protected function getPrgDataFromRequest()
    {
        $request = $this->getRequest();
        if (!$request instanceof Request) {
            return false;
        }

        if ($request->isXmlHttpRequest()) {
            if ($request->isPost()) {
                return $request->getPost()->toArray();
            }
            return false;
        }

        return $this->prg();
    }
}
