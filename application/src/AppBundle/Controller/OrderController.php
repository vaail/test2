<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Building;
use AppBundle\Entity\Lift;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class OrderController extends BaseController
{

    /**
     * @Route("/order", name="order")
     * @Method("GET")
     */
    public function orderAction(Request $request)
    {
        $buildingId = $request->query->get('building_id');
        if(is_null($buildingId)) {
            return $this->makeJsonErrorResponse('You need specify building_id param');
        }

        $floor = $request->query->get('floor');
        if(is_null($floor)) {
            return $this->makeJsonErrorResponse('You need specify floor param');
        } else if($floor <= 0) {
            return $this->makeJsonErrorResponse('Floor must be positive number');
        }

        $targetFloor = $request->query->get('target_floor');
        if(!is_null($targetFloor)) {
            $targetFloor = (int)$targetFloor;
            if($targetFloor <= 0) {
                return $this->makeJsonErrorResponse('Target floor must be positive number');
            }
        }

        $direction = null;
        if(is_null($targetFloor)) {
            $direction = $request->query->get('direction');
            if(!is_null($direction) && !in_array(strtolower($direction), ['up', 'down'])) {
                return $this->makeJsonErrorResponse('Direction floor must be \'up\' or \'down\'');
            }

            if(is_null($direction)) {
                $targetFloor = $floor;
            }
        }



        $buildingRepository = $this->getDoctrine()->getRepository(Building::class);
        $building = $buildingRepository->find($buildingId);
        if(is_null($building)) {
            return $this->makeJsonNotFoundResponse('Building not found');
        }

        if($building->getFloorsCount() < $floor) {
            return $this->makeJsonErrorResponse('This floor not exiting in building');
        }

        if(!is_null($targetFloor) && $building->getFloorsCount() < $targetFloor) {
            return $this->makeJsonErrorResponse('Target floor not exiting in building');
        }

        $data = $this->getAvailableLifts($buildingId, $floor, $targetFloor);
        $data = $this->getNearestLifts($data, $floor);

        $lift = $data[array_rand($data, 1)];
        if(!is_null($direction)) {
            if (strtolower($direction) === 'up') {
                $floors = $this->getUpFloors($lift, $floor);
                $targetFloor = $floors[array_rand($floors, 1)];
            } else if (strtolower($direction) === 'down') {
                $floors = $this->getDownFloors($lift, $floor);
                $targetFloor = $floors[array_rand($floors, 1)];
            }
        }

        $this->storeLiftFloor($lift, $targetFloor);

        return $this->makeJsonResponse($lift, 200, [], false, (function () {
            $encoders = [new JsonEncoder()];
            $normalizers = [(new ObjectNormalizer())->setIgnoredAttributes(array('building'))];

            $serializer = new Serializer($normalizers, $encoders);

            return $serializer;
        })());
    }

    private function getAvailableLifts($buildingId, $floor, $targetFloor = null) {
        $data = $this
            ->getDoctrine()
            ->getManager()
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

    private function getNearestLifts($lifts, $floor) {
        $lengths = array_map(function($lift) use ($floor) {
            return max($lift->getCurrentFloor(), $floor) - min($lift->getCurrentFloor(), $floor);
        }, $lifts);
        $minLength = min($lengths);

        return array_filter($lifts, function($item, $index) use ($lengths, $minLength) {
            return $lengths[$index] === $minLength;
        },ARRAY_FILTER_USE_BOTH);
    }

    private function storeLiftFloor(Lift $lift, $floor) {
        $lift->setCurrentFloor($floor);
        $this->getDoctrine()->getManager()->flush();
    }

    private function getUpFloors(Lift $lift, $floor) {
        $availableFloors = $lift->getAvailableFloors()->toArray();

        return array_filter($availableFloors, function($item) use ($floor) {
            return $item > $floor;
        });
    }

    private function getDownFloors(Lift $lift, $floor) {
        $availableFloors = $lift->getAvailableFloors()->toArray();

        return array_filter($availableFloors, function($item) use ($floor) {
            return $item < $floor;
        });
    }
}
