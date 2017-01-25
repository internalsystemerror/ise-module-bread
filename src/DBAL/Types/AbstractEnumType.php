<?php

namespace Ise\Bread\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use ReflectionClass;

abstract class AbstractEnumType extends Type
{
    /**
     * @var string
     */
    protected $name = '';
    
    /**
     * @static
     * @var array
     */
    protected $validValues = [];
    
    /**
     * Get choices (labels) for this type
     *
     * @return array
     */
    public static function getChoices()
    {
        return static::$validValues;
    }
    
    /**
     * Get values (keys) for this type
     *
     * @return array
     */
    public static function getValues()
    {
        return array_keys(static::getChoices());
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name ?: (new ReflectionClass(get_class($this)))->getShortName();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        if (!$platform instanceof MySqlPlatform) {
            return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
        }
        
        $values = implode(', ', array_map([$this, 'mapValue'], $this->getValues()));
        return sprintf('ENUM(%s)', $values);
    }
    
    /**
     * {@inheritDoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!isset(static::$validValues[$value])) {
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
     * {@inheritDoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return (string) $value;
    }

    /**
     * {@inheritDoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
    
    /**
     * Map value to string
     *
     * @param string $value
     * @return string
     */
    protected function mapValue($value)
    {
        return "'$value'";
    }
}
