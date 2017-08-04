<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Building
 *
 * @ORM\Table(name="building")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BuildingRepository")
 */
class Building
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="floors_count", type="integer", options={"default" : 1, "unsigned" : true})
     */
    private $floorsCount;

    /**
     * @ORM\OneToMany(targetEntity="Lift", mappedBy="building")
     */
    private $lifts;

    public function __construct()
    {
        $this->lifts = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Building
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

    /**
     * Set floorsCount
     *
     * @param integer $floorsCount
     *
     * @return Building
     */
    public function setFloorsCount($floorsCount)
    {
        $this->floorsCount = $floorsCount;

        return $this;
    }

    /**
     * Get floorsCount
     *
     * @return int
     */
    public function getFloorsCount()
    {
        return $this->floorsCount;
    }

    /**
     * Get lifts
     *
     * @return Lift[]
     */
    public function getLifts()
    {
        return $this->lifts;
    }
}

