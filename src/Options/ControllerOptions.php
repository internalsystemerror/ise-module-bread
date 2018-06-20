<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Options;

use Ise\Bread\Controller\BreadActionController;
use Ise\Bread\Controller\ControllerInterface;
use Ise\Bread\Controller\Factory\BreadActionControllerFactory;
use Ise\Bread\Entity\EntityInterface;
use Ise\Bread\EventManager\BreadEvent;
use Zend\Stdlib\ArrayUtils;

class ControllerOptions extends AbstractFactoryClassOptions
{

    /**
     * @var string
     */
    protected $baseClass = BreadActionController::class;

    /**
     * @var string
     */
    protected $factory = BreadActionControllerFactory::class;

    /**
     * @var string
     */
    protected $entityClass = '';

    /**
     * @var string
     */
    protected $entityTitle = '';

    /**
     * @var string
     */
    protected $indexRoute = '';

    /**
     * @var string
     */
    protected $basePermission = '';

    /**
     * @var string[]
     */
    protected $templates = [
        BreadEvent::EVENT_INDEX  => 'ise/bread/bread/browse',
        BreadEvent::EVENT_CREATE => 'ise/bread/bread/add',
        BreadEvent::EVENT_READ   => 'ise/bread/bread/read',
        BreadEvent::EVENT_UPDATE => 'ise/bread/bread/edit',
        BreadEvent::EVENT_DIALOG => 'ise/bread/bread/dialog',
    ];

    /**
     * @inheritdoc
     * @throws \ReflectionException
     */
    public function setBaseClass(string $class): void
    {
        $this->classImplementsInterface($class, ControllerInterface::class);
        parent::setBaseClass($class);
    }

    /**
     * Get entity class
     *
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * Set entity class
     *
     * @param string $entityClass
     *
     * @return void
     * @throws \ReflectionException
     */
    public function setEntityClass(string $entityClass): void
    {
        $this->classImplementsInterface($entityClass, EntityInterface::class);
        $this->entityClass = $entityClass;
    }

    /**
     * Get entity title
     *
     * @return string
     */
    public function getEntityTitle(): string
    {
        return $this->entityTitle;
    }

    /**
     * Set entity title
     *
     * @param string $entityTitle
     *
     * @return void
     */
    public function setEntityTitle(string $entityTitle): void
    {
        $this->entityTitle = $entityTitle;
    }

    /**
     * Get index route
     *
     * @return string
     */
    public function getIndexRoute(): string
    {
        return $this->indexRoute;
    }

    /**
     * Set index route
     *
     * @param string $indexRoute
     *
     * @return void
     */
    public function setIndexRoute(string $indexRoute): void
    {
        $this->indexRoute = $indexRoute;
    }

    /**
     * Get base permission
     *
     * @return string
     */
    public function getBasePermission(): string
    {
        return $this->basePermission;
    }

    /**
     * Set base permission
     *
     * @param string $permission
     *
     * @return void
     */
    public function setBasePermission(string $permission): void
    {
        $this->basePermission = $permission;
    }

    /**
     * Get templates
     *
     * @return string[]
     */
    public function getTemplates(): array
    {
        return $this->templates;
    }

    /**
     * Set templates
     *
     * An array of template names, indexed by the action name.
     *
     * @param string[] $templates
     *
     * @return void
     */
    public function setTemplates(array $templates): void
    {
        $this->templates = ArrayUtils::merge($this->templates, $templates);
    }
}
