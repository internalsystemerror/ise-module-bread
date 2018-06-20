<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Ise\Bread\Form\Annotation\AnnotationBuilder;
use Zend\ServiceManager\Factory\FactoryInterface;

class AnnotationBuilderFactory implements FactoryInterface
{

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AnnotationBuilder
    {
        return new $requestedName($container->get(EntityManager::class));
    }
}
