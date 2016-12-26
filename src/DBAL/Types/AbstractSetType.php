<?php

namespace IseBread\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use InvalidArgumentException;

abstract class AbstractSetType extends AbstractEnumType
{
    
    /**
     * {@inheritDoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        if (!$platform instanceof MySqlPlatform) {
            return $platform->getClobTypeDeclarationSQL($fieldDeclaration);
        }
        
        $values = implode(', ', array_map(array($this, 'mapValue'), $this->getValues()));
        return sprintf('SET(%s)', $values);
    }
    
    /**
     * {@inheritDoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
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
     * {@inheritDoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
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
