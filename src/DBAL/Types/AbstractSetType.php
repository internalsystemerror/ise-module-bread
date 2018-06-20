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

abstract class AbstractSetType extends Type
{

    use EnumTrait;

    /**
     * @inheritdoc
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        if (!$platform instanceof MySqlPlatform) {
            return $platform->getClobTypeDeclarationSQL($fieldDeclaration);
        }

        $values = implode(', ', array_map([$this, 'mapValue'], $this->getValues()));
        return sprintf('SET(%s)', $values);
    }

    /**
     * @inheritdoc
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (!is_array($value) || count($value) <= 0) {
            return null;
        }

        $invalidValues = array_diff($value, $this->getValues());

        if (count($invalidValues) > 0) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid value "%s". It is not defined in "%s::$validValues"',
                    implode(',', $value),
                    get_class($this)
                )
            );
        }

        return implode(',', $value);
    }

    /**
     * @inheritdoc
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (strpos($value, ',') === false) {
            return [$value];
        }

        return explode(',', $value);
    }
}
