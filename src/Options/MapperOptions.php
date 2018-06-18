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
     * {@inheritDoc}
     */
    public function setBaseClass($class)
    {
        $this->classImplementsInterface($class, MapperInterface::class);
        return parent::setBaseClass($class);
    }
}
