<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Entity;

interface BasicEntityInterface extends EntityInterface
{

    /**
     * Set name
     *
     * @param string $name
     *
     * @return void
     */
    public function setName($name): void;

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set description
     *
     * @param string|null $description
     *
     * @return void
     */
    public function setDescription(string $description = null): void;

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): string;
}
