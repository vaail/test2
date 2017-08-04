<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Lift
 *
 * @ORM\Table(name="lift", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="building_id_code", columns={"building_id", "code"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LiftRepository")
 */
class Lift
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
     * @var int
     *
     * @ORM\Column(name="building_id", type="integer")
     */
    private $buildingId;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, options={"default" : 1, "unsigned" : true})
     */
    private $code;

    /**
     * @var int
     *
     * @ORM\Column(name="current_floor", type="integer")
     */
    private $currentFloor;

    /**
     * @ORM\ManyToOne(targetEntity="Building", inversedBy="lift")
     * @ORM\JoinColumn(name="building_id", referencedColumnName="id")
     */
    private $building;

    /**
     * @ORM\OneToMany(targetEntity="LiftAvailableFloor", mappedBy="lift")
     */
    private $availableFloors;


    public function __construct()
    {
        $this->availableFloors = new ArrayCollection();
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
     * Set buildingId
     *
     * @param integer $buildingId
     *
     * @return Lift
     */
    public function setBuildingId($buildingId)
    {
        $this->buildingId = $buildingId;

        return $this;
    }

    /**
     * Get buildingId
     *
     * @return int
     */
    public function getBuildingId()
    {
        return $this->buildingId;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Lift
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set currentFloor
     *
     * @param integer $currentFloor
     *
     * @return Lift
     */
    public function setCurrentFloor($currentFloor)
    {
        $this->currentFloor = $currentFloor;

        return $this;
    }

    /**
     * Get currentFloor
     *
     * @return int
     */
    public function getCurrentFloor()
    {
        return $this->currentFloor;
    }

    /**
     * Set building
     *
     * @param Building $building
     *
     * @return Lift
     */
    public function setBuilding(Building $building)
    {
        $this->building = $building;

        return $this;
    }

    /**
     * Get building
     *
     * @return Building
     */
    public function getBuilding()
    {
        return $this->building;
    }

    public function getAvailableFloors()
    {
        return $this->availableFloors->map(function ($item) {
            return $item->getFloor();
        });
    }
}

