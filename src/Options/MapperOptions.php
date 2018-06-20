<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Options;

use Ise\Bread\Factory\BreadDoctrineOrmMapperFactory;
use Ise\Bread\Mapper\DoctrineOrm\BreadMapper;
use Ise\Bread\Mapper\MapperInterface;

class MapperOptions extends AbstractFactoryClassOptions
{

    /**
     * @var string
     */
    protected $baseClass = BreadMapper::class;

    /**
     * @var string
     */
    protected $factory = BreadDoctrineOrmMapperFactory::class;

    /**
     * @inheritdoc
     * @throws \ReflectionException
     */
    public function setBaseClass(string $class): void
    {
        $this->classImplementsInterface($class, MapperInterface::class);
        parent::setBaseClass($class);
    }
}
