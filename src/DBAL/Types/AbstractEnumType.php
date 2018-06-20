<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;

abstract class AbstractEnumType extends Type
{

    use EnumTrait;

    /**
     * @inheritdoc
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        if (!$platform instanceof MySqlPlatform) {
            return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
        }

        $values = implode(', ', array_map([$this, 'mapValue'], $this->getValues()));
        return sprintf('ENUM(%s)', $values);
    }

    /**
     * @inheritdoc
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!static::$validValues[$value]) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid value "%s". It is not defined in "%s::$validValues"',
                    $value,
                    get_class($this)
                )
            );
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): string
    {
        return (string)$value;
    }
}
