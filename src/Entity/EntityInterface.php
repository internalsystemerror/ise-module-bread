<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Entity;

use DateTime;

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
    public function __toString();

    /**
     * Get id
     *
     * @return string
     */
    public function getId();

    /**
     * Set disabled
     *
     * @param boolean $disabled
     *
     * @return self
     */
    public function setDisabled($disabled);

    /**
     * Is disabled
     *
     * @return boolean
     */
    public function isDisabled();

    /**
     * Set last modified
     *
     * @param DateTime $lastModifed Last modified date
     *
     * @return self
     */
    public function setLastModified(DateTime $lastModifed);

    /**
     * Get last modified
     *
     * @return DateTime
     */
    public function getLastModified();

    /**
     * Set created
     *
     * @param DateTime $created Created date
     *
     * @return self
     */
    public function setCreated(DateTime $created);

    /**
     * Get created
     *
     * @return DateTime
     */
    public function getCreated();
}
