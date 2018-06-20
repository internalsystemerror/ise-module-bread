<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\DBAL\Types;

use DateInterval;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\StringType;

class DateIntervalType extends StringType
{
    const DATE_INTERVAL = 'dateinterval';

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return self::DATE_INTERVAL;
    }

    /**
     * @inheritdoc
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
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
     * @inheritdoc
     * @throws \Exception
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): \DateInterval
    {
        return new \DateInterval((string)$value);
    }
}
