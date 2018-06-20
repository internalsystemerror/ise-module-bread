<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Controller;

use Ise\Bread\Entity\EntityInterface;
use Ise\Bread\EventManager\BreadEvent;
use Ise\Bread\EventManager\BreadEventManager;
use Ise\Bread\Options\ControllerOptions;
use Ise\Bread\Service\ServiceInterface;
use Zend\Filter\Word\CamelCaseToSeparator;
use Zend\Form\FieldsetInterface;
use Zend\Form\FormInterface;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController as ZendAbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\Stdlib\ResponseInterface;
use Zend\View\Model\ViewModel;

/**
 */

/**
 * Class BreadActionController
 *
 * @package Ise\Bread\Controller
 * @method FlashMessenger flashMessenger()
 * @method iterable|bool|Response prg()
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
    protected $basePermission;

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
     * @param BreadEventManager $breadEventManager
     * @param ServiceInterface  $service
     * @param ControllerOptions $options
     */
    public function __construct(
        BreadEventManager $breadEventManager,
        ServiceInterface $service,
        ControllerOptions $options
    ) {
        $this->breadEventManager = $breadEventManager;
        $this->service           = $service;
        $this->basePermission    = $options->getBasePermission();
        $this->entityClass       = $options->getEntityClass();
        $this->indexRoute        = $options->getIndexRoute();
        $this->entityTitle       = $options->getEntityTitle();
        $this->templates         = $options->getTemplates();
        $this->attachDefaultBreadListeners();
    }

    /**
     * @inheritdoc
     */
    public function browseAction()
    {
        return $this->triggerActionEvent(BreadEvent::EVENT_INDEX, BreadEvent::ACTION_INDEX);
    }

    /**
     * @inheritdoc
     */
    public function readAction()
    {
        return $this->triggerActionEvent(BreadEvent::EVENT_READ, BreadEvent::ACTION_READ);
    }

    /**
     * @inheritdoc
     */
    public function addAction()
    {
        return $this->triggerActionEvent(BreadEvent::EVENT_CREATE, BreadEvent::ACTION_CREATE, BreadEvent::FORM_CREATE);
    }

    /**
     * @inheritdoc
     */
    public function editAction()
    {
        return $this->triggerActionEvent(BreadEvent::EVENT_UPDATE, BreadEvent::ACTION_UPDATE, BreadEvent::FORM_UPDATE);
    }

    /**
     * @inheritdoc
     */
    public function deleteAction()
    {
        return $this->triggerActionEvent(BreadEvent::EVENT_DIALOG, BreadEvent::ACTION_DELETE, BreadEvent::FORM_DIALOG);
    }

    /**
     * @inheritdoc
     */
    public function enableAction()
    {
        return $this->triggerActionEvent(BreadEvent::EVENT_DIALOG, BreadEvent::ACTION_ENABLE, BreadEvent::FORM_DIALOG);
    }

    /**
     * @inheritdoc
     */
    public function disableAction()
    {
        return $this->triggerActionEvent(BreadEvent::EVENT_DIALOG, BreadEvent::ACTION_DISABLE, BreadEvent::FORM_DIALOG);
    }

    /**
     * Browse action event
     *
     * @param BreadEvent $event
     *
     * @return ViewModel
     */
    public function onActionBrowse(BreadEvent $event): ViewModel
    {
        $viewModel = $event->getViewModel();
        $viewModel->setVariable('list', $this->service->browse());

        return $viewModel;
    }

    /**
     * Read action event
     *
     * @param BreadEvent $event
     *
     * @return ViewModel
     */
    public function onActionRead(BreadEvent $event): ViewModel
    {
        $viewModel = $event->getViewModel();
        $viewModel->setVariable('entity', $event->getEntity());

        return $viewModel;
    }

    /**
     * Create action event
     *
     * @param BreadEvent $event
     *
     * @return ViewModel
     */
    public function onActionCreate(BreadEvent $event): ViewModel
    {
        $viewModel = $event->getViewModel();
        $viewModel->setVariable('form', $event->getForm());

        return $viewModel;
    }

    /**
     * Update action event
     *
     * @param BreadEvent $event
     *
     * @return ViewModel
     */
    public function onActionUpdate(BreadEvent $event): ViewModel
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
     * @param BreadEvent $event
     *
     * @return ViewModel
     */
    public function onActionDialog(BreadEvent $event): ViewModel
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
     *
     * @return null|Response
     */
    public function loadPrg(BreadEvent $event): ?Response
    {
        $prg = $this->getPrgDataFromRequest();
        if ($prg instanceof Response) {
            return $prg;
        }

        $event->setPrgData($prg);
    }

    /**
     * Load entity
     *
     * @param BreadEvent $event
     *
     * @return ViewModel|null
     */
    public function loadEntity(BreadEvent $event): ?ViewModel
    {
        // Load entity
        $entity = $this->getEntity();
        if ($entity) {
            $event->setEntity($entity);
            return null;
        }

        return $this->notFoundAction();
    }

    /**
     * Load form
     *
     * @param BreadEvent $event
     *
     * @return void
     */
    public function loadForm(BreadEvent $event): void
    {
        $form = $event->getForm();
        if (!$form instanceof FormInterface) {
            $form = $this->service->getForm($form);
        }

        $event->setForm($form);
    }

    /**
     * Bind entity to form
     *
     * @param BreadEvent $event
     *
     * @return void
     */
    public function bindEntityToForm(BreadEvent $event): void
    {
        $form = $event->getForm();
        if (!$form instanceof FormInterface) {
            return;
        }

        $entity = $event->getEntity();
        if (!$entity instanceof EntityInterface) {
            return;
        }

        $form->bind($entity);
    }

    /**
     * POST data to service
     *
     * @param BreadEvent $event
     *
     * @return Response|null
     */
    public function postToService(BreadEvent $event): ?Response
    {
        $action = $event->getAction();
        $data   = $event->getPrgData();
        if (!$data) {
            return null;
        }

        // Trigger service action
        $result = $this->service->$action($data);
        if (!$result) {
            return null;
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
     *
     * @return void
     */
    public function setupFormForView(BreadEvent $event): void
    {
        $form = $event->getForm();
        if (!$form instanceof FormInterface) {
            return;
        }

        $form->setAttribute('class', 'form-horizontal');
        $buttons = $form->get('buttons');
        if (!$buttons instanceof FieldsetInterface) {
            return;
        }

        $buttons->get('cancel')->setAttribute(
            'href',
            $this->url()->fromRoute($this->indexRoute)
        );
    }

    /**
     * Setup form for dialog
     *
     * @param BreadEvent $event
     *
     * @return void
     */
    public function setupFormForDialog(BreadEvent $event): void
    {
        $form = $event->getForm();
        if (!$form instanceof FormInterface) {
            return;
        }

        $buttons = $form->get('buttons');
        if (!$buttons instanceof FieldsetInterface) {
            return;
        }

        $buttons->get('cancel')->setAttributes([
            'data-href'    => $this->url()->fromRoute($this->indexRoute),
            'data-dismiss' => 'modal',
        ]);
    }

    /**
     * Check if dialog is not allowed
     *
     * @param BreadEvent $event
     *
     * @return Response|null
     */
    public function checkDialogNotAllowed(BreadEvent $event): ?Response
    {
        $entity = $event->getEntity();
        if (!$entity instanceof EntityInterface) {
            return null;
        }

        switch ($event->getAction()) {
            case BreadEvent::ACTION_DISABLE:
                if (!$entity->isDisabled()) {
                    return null;
                }
                $this->flashMessenger()->addWarningMessage(sprintf(
                    'That %s is already disabled',
                    $this->entityTitle
                ));
                return $this->redirect()->toRoute($this->indexRoute);
            case BreadEvent::ACTION_ENABLE:
                if ($entity->isDisabled()) {
                    return null;
                }
                $this->flashMessenger()->addWarningMessage(sprintf(
                    'That %s is already enabled',
                    $this->entityTitle
                ));
                return $this->redirect()->toRoute($this->indexRoute);
        }

        return null;
    }

    /**
     * Setup view model
     *
     * @param BreadEvent $event
     *
     * @return ViewModel
     */
    public function setupViewModel(BreadEvent $event): ViewModel
    {
        $viewModel = $event->getViewModel();
        $viewModel->setVariables([
            'basePermission' => ($this->basePermission),
            'indexRoute'     => ($this->indexRoute),
            'entityTitle'    => ucwords($this->entityTitle),
        ]);

        // Set up view model
        if ($this->templates[$event->getName()]) {
            $viewModel->setTemplate($this->templates[$event->getName()]);
        }

        return $viewModel;
    }

    /**
     * Wrap dialog view model
     *
     * @param BreadEvent $event
     *
     * @return ViewModel
     */
    public function wrapDialogViewModel(BreadEvent $event): ViewModel
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
     *
     * @return void
     */
    protected function attachDefaultBreadListeners(): void
    {
        $this->attachDefaultIndexListeners();
        $this->attachDefaultReadListeners();
        $this->attachDefaultCreateListeners();
        $this->attachDefaultUpdateListeners();
        $this->attachDefaultDialogListeners();
    }

    /**
     * Attach default index listeners
     *
     * @return void
     */
    protected function attachDefaultIndexListeners(): void
    {
        $this->breadEventManager->attach(BreadEvent::EVENT_INDEX, [$this, 'onActionBrowse']);
        $this->breadEventManager->attach(BreadEvent::EVENT_INDEX, [$this, 'setupViewModel'], -100);
    }

    /**
     * Attach default read listeners
     *
     * @return void
     */
    protected function attachDefaultReadListeners(): void
    {
        $this->breadEventManager->attach(BreadEvent::EVENT_READ, [$this, 'loadEntity'], 900);
        $this->breadEventManager->attach(BreadEvent::EVENT_READ, [$this, 'onActionRead']);
        $this->breadEventManager->attach(BreadEvent::EVENT_READ, [$this, 'setupViewModel'], -100);
    }

    /**
     * Attach default create listeners
     *
     * @return void
     */
    protected function attachDefaultCreateListeners(): void
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
     *
     * @return void
     */
    protected function attachDefaultUpdateListeners(): void
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
     *
     * @return void
     */
    protected function attachDefaultDialogListeners(): void
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
     * @param string      $name
     * @param null|string $action
     * @param null|string $form
     *
     * @return mixed
     */
    protected function triggerActionEvent(string $name, string $action = null, string $form = null)
    {
        if (!$action) {
            $action = $name;
        }

        // Setup new event
        $event = new BreadEvent;
        $event->setName($name);
        if ($action) {
            $event->setAction($action);
        }
        if ($form) {
            $event->setForm($form);
        }
        $event->setViewModel(new ViewModel);

        // Get result from action
        $result = $this->breadEventManager->triggerEventUntil(function ($test) {
            if ($test instanceof ResponseInterface) {
                return true;
            }

            $mvcEvent   = $this->getEvent();
            $routeMatch = $mvcEvent->getRouteMatch();
            if (!$routeMatch) {
                return true;
            }

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
     * @return EntityInterface|null
     */
    protected function getEntity(): ?EntityInterface
    {
        // Get entity id
        $id = (string)$this->params(BreadEvent::IDENTIFIER, '');
        if (!$id) {
            return null;
        }

        $entity = $this->service->read($id);
        if (!$entity) {
            return null;
        }
        return $entity;
    }

    /**
     * Redirect browse to index route
     *
     * @return Response
     */
    protected function redirectBrowse(): Response
    {
        // Redirect to index route
        return $this->redirect()->toRoute($this->indexRoute);
    }

    /**
     * Get PRG data from request
     *
     * @return bool|iterable|Response
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
