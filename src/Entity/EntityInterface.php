<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Entity;

interface EntityInterface
{

    /**
     * Constructor method
     */
    public function __construct();

    /**
     * Cast entity to a string
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Get id
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Set disabled
     *
     * @param bool $disabled
     *
     * @return void
     */
    public function setDisabled(bool $disabled): void;

    /**
     * Is disabled
     *
     * @return bool
     */
    public function isDisabled(): bool;

    /**
     * Set last modified
     *
     * @param \DateTime $lastModifed Last modified date
     *
     * @return void
     */
    public function setLastModified(\DateTime $lastModifed): void;

    /**
     * Get last modified
     *
     * @return \DateTime
     */
    public function getLastModified(): \DateTime;

    /**
     * Set created
     *
     * @param \DateTime $created Created date
     *
     * @return void
     */
    public function setCreated(\DateTime $created): void;

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated();
}
