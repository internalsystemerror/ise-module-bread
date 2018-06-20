<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

trait EnumTrait
{

    /**
     * @static
     * @var array
     */
    protected static $validValues = [];

    /**
     * @var string
     */
    protected $name = '';

    /**
     * Get choices (labels) for this type
     *
     * @return string[]
     */
    public static function getChoices(): array
    {
        return static::$validValues;
    }

    /**
     * Get values (keys) for this type
     *
     * @return array
     */
    public static function getValues(): array
    {
        return array_keys(static::getChoices());
    }

    /**
     * @inheritdoc
     * @throws \ReflectionException
     */
    public function getName(): string
    {
        return $this->name ?: (new \ReflectionClass(get_class($this)))->getShortName();
    }

    /**
     * @inheritdoc
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    /**
     * Map value to string
     *
     * @param string $value
     *
     * @return string
     */
    protected function mapValue($value): string
    {
        return "'$value'";
    }
}
