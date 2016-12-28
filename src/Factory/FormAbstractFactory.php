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
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use DoctrineORMModule\Form\Element\EntityRadio;
use DoctrineORMModule\Form\Element\EntitySelect;
use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity;
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
        $entityClass = $this->translateFormToEntity($requestedName);
        $entity      = new $entityClass;

        // Create builder
        $entityManager      = $container->get('Doctrine\ORM\EntityManager');
        $formElementManager = $container->get('FormElementManager');
        $builder            = new AnnotationBuilder($entityManager);
        $builder->getFormFactory()->setFormElementManager($formElementManager);

        // Choose value for submit button
        $translatedName = $this->translateFormToType($requestedName);
        switch ($translatedName) {
            case 'add':
            case 'edit':
                $submit = 'Save';
                $form   = $builder->createForm($entity);
                $this->injectEntityManagerIntoElements($form, $entityManager);
                break;
            case 'delete':
            default:
                $submit = 'Confirm';
                $form   = new Form();
                break;
        }

        // Add hydrator
        $hydrator = new DoctrineEntity($entityManager);
        $form->setHydrator($hydrator);
        if ($translatedName === 'add') {
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

    protected function addIdToForm(Form $form)
    {
        $form->add([
            'type' => 'hidden',
            'name' => 'id',
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
                    $element->getProxy()->setOptions(array(
                        'object_manager' => $entityManager,
                    ));
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
