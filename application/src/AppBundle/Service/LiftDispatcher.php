<?php

namespace AppBundle\Service;

use AppBundle\Entity\Lift;
use Doctrine\ORM\EntityManager;

/**
 * Class LiftDispatcher
 * @package AppBundle\Service
 */
class LiftDispatcher {

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * LiftDispatcher constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $buildingId
     * @param $floor
     * @param null $targetFloor
     * @return array
     */
    public function getAvailableLifts($buildingId, $floor, $targetFloor = null) {
        $data = $this->em
            ->getRepository(Lift::class)
            ->findBy(['buildingId' => $buildingId]);

        return array_filter($data, function($item) use ($floor, $targetFloor) {
            $availableFloors = $item->getAvailableFloors()->toArray();

            if(is_null($targetFloor)) {
                return in_array($floor, $availableFloors);
            } else {
                return in_array($floor, $availableFloors) && in_array($targetFloor, $availableFloors);
            }
        });
    }

    /**
     * @param $lifts
     * @param $floor
     * @return array
     */
    public function getNearestLifts($lifts, $floor) {
        $lengths = array_map(function($lift) use ($floor) {
            return max($lift->getCurrentFloor(), $floor) - min($lift->getCurrentFloor(), $floor);
        }, $lifts);
        $minLength = min($lengths);

        return array_filter($lifts, function($item, $index) use ($lengths, $minLength) {
            return $lengths[$index] === $minLength;
        },ARRAY_FILTER_USE_BOTH);
    }

    /**
     * @param Lift $lift
     * @param $floor
     */
    public function storeLiftFloor(Lift $lift, $floor) {
        $lift->setCurrentFloor((int)$floor);
        $this->em->flush();
    }

    /**
     * @param Lift $lift
     * @param $floor
     * @return array
     */
    public function getUpFloors(Lift $lift, $floor) {
        $availableFloors = $lift->getAvailableFloors()->toArray();

        return array_filter($availableFloors, function($item) use ($floor) {
            return $item > $floor;
        });
    }

    /**
     * @param Lift $lift
     * @param $floor
     * @return array
     */
    public function getDownFloors(Lift $lift, $floor) {
        $availableFloors = $lift->getAvailableFloors()->toArray();

        return array_filter($availableFloors, function($item) use ($floor) {
            return $item < $floor;
        });
    }
}