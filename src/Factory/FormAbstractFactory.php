<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Factory;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use DoctrineORMModule\Form\Element\EntityRadio;
use DoctrineORMModule\Form\Element\EntitySelect;
use Interop\Container\ContainerInterface;
use Ise\Bread\EventManager\BreadEvent;
use Ise\Bread\Form\Annotation\AnnotationBuilder;
use Ise\Bread\Form\Annotation\ElementAnnotationsListener;
use Zend\Form\Form;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class FormAbstractFactory implements AbstractFactoryInterface
{

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Form
    {
        // Create entity
        $formType    = $this->translateFormToType($requestedName);
        $entityClass = $this->translateFormToEntity($requestedName);
        $entity      = new $entityClass;

        // Create builder
        /** @var AnnotationBuilder $builder */
        $builder            = $container->get('doctrine.formannotationbuilder.orm_default');
        $formElementManager = $container->get('FormElementManager');
        $entityManager      = $container->get(EntityManager::class);
        $formFactory        = $builder->getFormFactory();
        $elementListener    = new ElementAnnotationsListener($entityManager, $formType);
        $elementListener->attach($builder->getEventManager());
        $formFactory->setFormElementManager($formElementManager);

        // Choose value for submit button
        switch ($formType) {
            case BreadEvent::FORM_CREATE:
                $submit = 'Create';
                $form   = $builder->createForm($entity);
                $this->injectEntityManagerIntoElements($form, $entityManager);
                $form->remove(BreadEvent::IDENTIFIER);
                $inputFilter = $form->getInputFilter();
                if ($inputFilter) {
                    $inputFilter->remove(BreadEvent::IDENTIFIER);
                }
                break;
            case BreadEvent::FORM_UPDATE:
                $submit = 'Save';
                $form   = $builder->createForm($entity);
                $this->injectEntityManagerIntoElements($form, $entityManager);
                break;
            case BreadEvent::FORM_DIALOG:
            default:
                $submit = 'Confirm';
                $form   = new Form;
                break;
        }

        // Assign hydrator
        $hydrator = new DoctrineObject($entityManager);
        $form->setHydrator($hydrator);
        $form->bind($entity);

        // Add security and submit elements
        $this->addButtonsToForm($form, $submit);

        return $form;
    }

    /**
     * @inheritdoc
     */
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        // Does entity class exist
        return (bool)$this->translateFormToEntity($requestedName);
    }

    /**
     * Add button fieldset to form
     *
     * @param Form   $form
     * @param string $submitText
     *
     * @return void
     */
    public function addButtonsToForm(Form $form, $submitText): void
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
        ], ['priority' => -100]);
    }

    /**
     * Inject entity manager into elements
     *
     * @param Form          $form
     * @param EntityManager $entityManager
     *
     * @return void
     */
    protected function injectEntityManagerIntoElements(Form $form, EntityManager $entityManager): void
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
    protected function specElementCsrf(): array
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
    protected function specElementCancel(): array
    {
        return [
            'spec' => [
                'type'       => 'button',
                'name'       => 'cancel',
                'options'    => [
                    'icon' => 'remove',
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
     *
     * @return array
     */
    protected function specElementSubmit(string $submitText): array
    {
        return [
            'spec' => [
                'type'       => 'button',
                'name'       => 'submit',
                'options'    => [
                    'icon' => 'ok',
                    'type' => 'primary',
                ],
                'attributes' => [
                    'type'  => 'submit',
                    'value' => $submitText,
                ],
            ],
        ];
    }

    /**
     * Translate form name to entity class
     *
     * @param  string $formName
     *
     * @return string|null
     */
    protected function translateFormToEntity(string $formName): ?string
    {
        $entityClass = trim(str_replace('Form', 'Entity', substr($formName, 0, strrpos($formName, '\\'))), '\\');
        if (!$entityClass) {
            return null;
        }
        $entityClass = '\\' . ucfirst($entityClass);
        return class_exists($entityClass) ? $entityClass : null;
    }

    /**
     * Translate form name to form type
     *
     * @param  string $formName
     *
     * @return string
     */
    protected function translateFormToType(string $formName): string
    {
        return lcfirst(substr($formName, strrpos($formName, '\\') + 1));
    }
}
