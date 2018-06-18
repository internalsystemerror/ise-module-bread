<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Ise\Bread\ServiceManager\BreadManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class BreadDoctrineOrmMapperFactory implements FactoryInterface
{

    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get(EntityManager::class);
        $breadManager  = $container->get(BreadManager::class);
        $mapperClass   = $breadManager->getMapperBaseClass($requestedName);

        return new $mapperClass(
            $entityManager,
            $entityManager->getRepository($breadManager->getEntityClassFromMapperClass($requestedName))
        );
    }
}
