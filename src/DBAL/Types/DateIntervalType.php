<?php

namespace IseBread\DBAL\Types;

use DateInterval;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class DateIntervalType extends StringType
{
    const DATE_INTERVAL = 'dateinterval';
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return self::DATE_INTERVAL;
    }
    
    /**
     * {@inheritDoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }
        
        if (!$value instanceof DateInterval) {
            throw new ConversionException(
                'DateInterval class expected, "' . gettype($value) . '" given.'
            );
        }
        
        return 'P' . $value->format('%r%yY%r%mM%r%dDT%r%hH%r%iM%r%sS');
    }
    
    /**
     * {@inheritDoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new DateInterval((string)$value);
    }
}
