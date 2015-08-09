<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
class NamedEntity extends BaseEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;


    /**
     * Set name
     *
     * @param string $name
     * @return NamedEntity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
