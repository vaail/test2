<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LiftAvailableFloor
 *
 * @ORM\Table(name="lift_available_floor", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="lift_id_floor", columns={"lift_id", "floor"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LiftAvailableFloorRepository")
 */
class LiftAvailableFloor
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
     * @ORM\Column(name="lift_id", type="integer")
     */
    private $liftId;

    /**
     * @var int
     *
     * @ORM\Column(name="floor", type="integer")
     */
    private $floor;

    /**
     * @ORM\ManyToOne(targetEntity="Lift", inversedBy="liftAvailableFloor")
     * @ORM\JoinColumn(name="lift_id", referencedColumnName="id")
     */
    private $lift;


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
     * Get liftId
     *
     * @return int
     */
    public function getLiftId()
    {
        return $this->liftId;
    }

    /**
     * Set floor
     *
     * @param integer $floor
     *
     * @return LiftAvailableFloor
     */
    public function setFloor($floor)
    {
        $this->floor = $floor;

        return $this;
    }

    /**
     * Get floor
     *
     * @return int
     */
    public function getFloor()
    {
        return $this->floor;
    }

    /**
     * Set lift
     *
     * @param Lift $lift
     *
     * @return LiftAvailableFloor
     */
    public function setLift(Lift $lift)
    {
        $this->lift = $lift;

        return $this;
    }

    /**
     * Get lift
     *
     * @return Lift
     */
    public function getLift()
    {
        return $this->lift;
    }
}

