<?php

namespace Ise\Bread\Entity;

interface BasicEntityInterface extends EntityInterface
{

    /**
     * Set name
     *
     * @param string $name
     * @return Permission
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set description
     *
     * @param string $description
     * @return Permission
     */
    public function setDescription($description);

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription();
}
