<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\EventManager;

use Ise\Bread\Entity\EntityInterface;
use Ise\Bread\Exception\InvalidArgumentException;
use Zend\EventManager\Event;
use Zend\Form\FormInterface;
use Zend\View\Model\ViewModel;

class BreadEvent extends Event
{

    /**
     * The identifier key
     */
    const IDENTIFIER = 'id';

    /**
     * Available bread actions
     */
    const ACTION_INDEX   = 'browse';
    const ACTION_READ    = 'read';
    const ACTION_CREATE  = 'add';
    const ACTION_UPDATE  = 'edit';
    const ACTION_DELETE  = 'delete';
    const ACTION_ENABLE  = 'enable';
    const ACTION_DISABLE = 'disable';

    /**
     * Available bread forms
     */
    const FORM_CREATE = self::ACTION_CREATE;
    const FORM_UPDATE = self::ACTION_UPDATE;
    const FORM_DIALOG = 'dialog';

    /**
     * Bread events triggered by event manager
     */
    const EVENT_INDEX  = self::ACTION_INDEX;
    const EVENT_READ   = self::ACTION_READ;
    const EVENT_CREATE = self::FORM_CREATE;
    const EVENT_UPDATE = self::FORM_UPDATE;
    const EVENT_DIALOG = self::FORM_DIALOG;

    /**
     * @var string[]
     */
    protected static $availableActions = [
        self::ACTION_READ,
        self::ACTION_CREATE,
        self::ACTION_UPDATE,
        self::ACTION_DELETE,
        self::ACTION_ENABLE,
        self::ACTION_DISABLE,
    ];

    /**
     * @var string[]
     */
    protected static $availableForms = [
        self::FORM_CREATE,
        self::FORM_UPDATE,
        self::FORM_DIALOG,
    ];

    /**
     * @var string
     */
    protected $action;

    /**
     * @var EntityInterface
     */
    protected $entity;

    /**
     * @var string|FormInterface
     */
    protected $form;

    /**
     * @var bool|array
     */
    protected $prgData;

    /**
     * @var ViewModel
     */
    protected $viewModel;

    /**
     * Get the list of available bread actions
     *
     * @return string[]
     */
    public static function getAvailableActions(): array
    {
        return static::$availableActions;
    }

    /**
     * Get the list of available forms
     *
     * @return string[]
     */
    public static function getAvailableForms(): array
    {
        return static::$availableForms;
    }

    /**
     * Get the action
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Set the action
     *
     * @param string $action
     *
     * @return void
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * Get the entity
     *
     * @return EntityInterface
     */
    public function getEntity(): EntityInterface
    {
        return $this->entity;
    }

    /**
     * Set the entity
     *
     * @param EntityInterface $entity
     *
     * @return void
     */
    public function setEntity(EntityInterface $entity): void
    {
        $this->entity = $entity;
    }

    /**
     * Get the form
     *
     * @return string|FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set the form
     *
     * @param string|FormInterface $form
     *
     * @return void
     */
    public function setForm($form): void
    {
        if (!is_string($form) && !$form instanceof FormInterface) {
            throw new InvalidArgumentException(sprintf(
                'String or instance of %s expected. %s given.',
                FormInterface::class,
                is_object($form) ? get_class($form) : gettype($form)
            ));
        }

        $this->form = $form;
    }

    /**
     * Get the PRG data
     *
     * @return bool|array
     */
    public function getPrgData()
    {
        return $this->prgData;
    }

    /**
     * Set the PRG data
     *
     * @param bool|iterable $data
     *
     * @return void
     */
    public function setPrgData($data): void
    {
        if ($data !== false && !is_array($data)) {
            throw new InvalidArgumentException(sprintf(
                'String or array expected. %s given.',
                is_object($data) ? get_class($data) : gettype($data)
            ));
        }

        $this->prgData = $data;
    }

    /**
     * Get the view model
     *
     * @return ViewModel
     */
    public function getViewModel(): ViewModel
    {
        return $this->viewModel;
    }

    /**
     * Set the view model
     *
     * @param ViewModel $viewModel
     *
     * @return void
     */
    public function setViewModel(ViewModel $viewModel): void
    {
        $this->viewModel = $viewModel;
    }
}
