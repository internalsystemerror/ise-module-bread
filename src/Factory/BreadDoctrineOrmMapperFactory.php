<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Ise\Bread\Exception\InvalidArgumentException;
use Ise\Bread\Mapper\DoctrineOrm\MapperInterface;
use Ise\Bread\ServiceManager\BreadManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class BreadDoctrineOrmMapperFactory implements FactoryInterface
{

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): MapperInterface
    {
        $entityManager = $container->get(EntityManager::class);
        $breadManager  = $container->get(BreadManager::class);
        $mapperClass   = $breadManager->getMapperBaseClass($requestedName);
        if (!$mapperClass) {
            throw new InvalidArgumentException('Mapper class not found for ' . $requestedName);
        }

        return new $mapperClass(
            $entityManager,
            $entityManager->getRepository($breadManager->getEntityClassFromMapperClass($requestedName))
        );
    }
}
