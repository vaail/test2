<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Building;
use AppBundle\Service\LiftDispatcher;
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
        $liftDispatcher = $this->get(LiftDispatcher::class);
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
        if(!is_null($targetFloor) && $targetFloor <= 0) {
            return $this->makeJsonErrorResponse('Target floor must be positive number');
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

        $data = $liftDispatcher->getAvailableLifts($buildingId, $floor, $targetFloor);
        $data = $liftDispatcher->getNearestLifts($data, $floor);

        $lift = $data[array_rand($data, 1)];
        if(!is_null($direction)) {
            if (strtolower($direction) === 'up') {
                $floors = $liftDispatcher->getUpFloors($lift, $floor);
                $targetFloor = $floors[array_rand($floors, 1)];
            } else if (strtolower($direction) === 'down') {
                $floors = $liftDispatcher->getDownFloors($lift, $floor);
                $targetFloor = $floors[array_rand($floors, 1)];
            }
        }

        $liftDispatcher->storeLiftFloor($lift, $targetFloor);

        return $this->makeJsonResponse($lift, 200, [], false, (function () {
            $encoders = [new JsonEncoder()];
            $normalizers = [(new ObjectNormalizer())->setIgnoredAttributes(array('building'))];

            $serializer = new Serializer($normalizers, $encoders);

            return $serializer;
        })());
    }
}
