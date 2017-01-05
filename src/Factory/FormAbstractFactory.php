<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Ise\Bread\Factory;

use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use DoctrineORMModule\Form\Element\EntityRadio;
use DoctrineORMModule\Form\Element\EntitySelect;
use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity;
use Ise\Bread\Form\Annotation\ElementAnnotationsListener;
use Ise\Bread\Router\Http\BreadRouteStack;
use Interop\Container\ContainerInterface;
use Zend\Form\Form;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormAbstractFactory implements AbstractFactoryInterface
{
    
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Create entity
        $actionType  = $this->translateFormToType($requestedName);
        $entityClass = $this->translateFormToEntity($requestedName);
        $entity      = new $entityClass;

        // Create builder
        $entityManager      = $container->get('Doctrine\ORM\EntityManager');
        $builder            = $container->get('doctrine.formannotationbuilder.orm_default');
        $elementListener    = new ElementAnnotationsListener($entityManager, $actionType);
        $elementListener->attach($builder->getEventManager());

        // Choose value for submit button
        switch ($actionType) {
            case BreadRouteStack::ACTION_CREATE:
            case BreadRouteStack::ACTION_UPDATE:
                $submit = 'Save';
                $form   = $builder->createForm($entity);
                $this->injectEntityManagerIntoElements($form, $entityManager);
                break;
            default:
                $submit = 'Confirm';
                $form   = new Form();
                break;
        }

        // Add hydrator
        $hydrator = new DoctrineEntity($entityManager);
        $form->setHydrator($hydrator);
        if ($actionType === BreadRouteStack::ACTION_CREATE) {
            $form->remove(BreadRouteStack::IDENTIFIER);
            $form->getInputFilter()->remove(BreadRouteStack::IDENTIFIER);
            $form->bind($entity);
        }

        // Add security and submit elements
        $this->addButtonsToForm($form, $submit);

        return $form;
    }
    
    /**
     * {@inheritDoc}
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        // Does entity class exist
        return (bool) $this->translateFormToEntity($requestedName);
    }
    
    /**
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->canCreate($serviceLocator, $requestedName);
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this($serviceLocator, $requestedName);
    }

    /**
     * Add button fieldset to form
     *
     * @param Form   $form
     * @param string $submitText
     */
    public function addButtonsToForm(Form $form, $submitText)
    {
        $form->add([
            'type'       => 'fieldset',
            'name'       => 'buttons',
            'attributes' => [
                'class' => 'control-group-buttons',
            ],
            'elements'   => [
                $this->specElementCsrf(),
                $this->specElementCancel(),
                $this->specElementSubmit($submitText),
            ],
        ]);
    }

    /**
     * Inject entity manager into elements
     *
     * @param Form          $form
     * @param EntityManager $entityManager
     */
    protected function injectEntityManagerIntoElements(Form $form, EntityManager $entityManager)
    {
        foreach ($form->getElements() as $element) {
            switch (true) {
                case $element instanceof EntityMultiCheckbox:
                case $element instanceof EntityRadio:
                case $element instanceof EntitySelect:
                    $element->getProxy()->setOptions([
                        'object_manager' => $entityManager,
                    ]);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * CSRF element spec
     *
     * @return array
     */
    protected function specElementCsrf()
    {
        return [
            'spec' => ['type' => 'csrf', 'name' => 'security'],
        ];
    }

    /**
     * Cancel element spec
     *
     * @return array
     */
    protected function specElementCancel()
    {
        return [
            'spec' => [
                'type'       => 'button',
                'name'       => 'cancel',
                'options'    => [
                    'icon' => 'remove'
                ],
                'attributes' => [
                    'class' => 'btn-cancel',
                    'value' => 'Cancel',
                ],
            ],
        ];
    }

    /**
     * Submit element spec
     *
     * @param  string $submitText
     * @return array
     */
    protected function specElementSubmit($submitText)
    {
        return [
            'spec' => [
                'type'       => 'submit',
                'name'       => 'submit',
                'options'    => [
                    'icon' => 'tick',
                ],
                'attributes' => [
                    'value' => $submitText,
                    'class' => 'btn btn-primary',
                ],
            ],
        ];
    }

    /**
     * Translate form name to entity class
     *
     * @param  string $formName
     * @return boolean
     */
    protected function translateFormToEntity($formName)
    {
        $entityClass = trim(str_replace('Form', 'Entity', substr($formName, 0, strrpos($formName, '\\'))), '\\');
        if (!$entityClass) {
            return false;
        }
        $entityClass = '\\' . ucfirst($entityClass);
        return class_exists($entityClass) ? $entityClass : false;
    }

    /**
     * Translate form name to form type
     *
     * @param  string $formName
     * @return string
     */
    protected function translateFormToType($formName)
    {
        return lcfirst(substr($formName, strrpos($formName, '\\') + 1));
    }
}
