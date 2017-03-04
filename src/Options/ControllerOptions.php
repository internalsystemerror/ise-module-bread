<?php

namespace Ise\Bread\Options;

use Ise\Bread\Controller\Factory\BreadActionControllerFactory;
use Ise\Bread\Controller\BreadActionController;
use Ise\Bread\Controller\ControllerInterface;
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
        BreadEvent::EVENT_DIALOG   => 'ise/bread/bread/dialog',
    ];
    
    /**
     * {@inheritDoc}
     */
    public function setBaseClass($class)
    {
        $this->classImplementsInterface($class, ControllerInterface::class);
        return parent::setBaseClass($class);
    }

    /**
     * Set entity class
     *
     * @param string $entityClass
     * @return self
     */
    public function setEntityClass($entityClass)
    {
        $this->classImplementsInterface($entityClass, EntityInterface::class);
        $this->entityClass = (string) $entityClass;
        return $this;
    }
    
    /**
     * Get entity class
     *
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }
    
    /**
     * Set entity title
     *
     * @param string $entityTitle
     * @return self
     */
    public function setEntityTitle($entityTitle)
    {
        $this->entityTitle = (string) $entityTitle;
        return $this;
    }
    
    /**
     * Get entity title
     *
     * @return string
     */
    public function getEntityTitle()
    {
        return $this->entityTitle;
    }
    
    /**
     * Set index route
     *
     * @param string $indexRoute
     * @return self
     */
    public function setIndexRoute($indexRoute)
    {
        $this->indexRoute = (string) $indexRoute;
        return $this;
    }
    
    /**
     * Get index route
     *
     * @return string
     */
    public function getIndexRoute()
    {
        return $this->indexRoute;
    }
    
    /**
     * Set base permission
     *
     * @params string $permission
     * @return self
     */
    public function setBasePermission($permission)
    {
        $this->basePermission = (string) $permission;
        return $this;
    }
    
    /**
     * Get base permission
     *
     * @return string
     */
    public function getBasePermission()
    {
        return $this->basePermission;
    }

    /**
     * Set templates
     *
     * An array of template names, indexed by the action name.
     *
     * @param string[] $templates
     * @return self
     */
    public function setTemplates(array $templates)
    {
        $this->templates = ArrayUtils::merge($this->templates, $templates);
        return $this;
    }

    /**
     * Get templates
     *
     * @return string[]
     */
    public function getTemplates()
    {
        return $this->templates;
    }
}
