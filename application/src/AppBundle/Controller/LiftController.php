<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Building;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class LiftController extends BaseController
{

    /**
     * @Route("/lift", name="lifts")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $building_id = $request->query->get('building_id');
        if(is_null($building_id)) {
            return $this->makeJsonErrorResponse('You need specify building_id param');
        }

        $buildingRepository = $this->getDoctrine()->getRepository(Building::class);
        $building = $buildingRepository->find($building_id);
        if(is_null($building)) {
            return $this->makeJsonNotFoundResponse('Building not found');
        }

        return $this->makeJsonResponse($building->getLifts(), 200, [], false, (function () {
            $encoders = [new JsonEncoder()];
            $normalizers = [(new ObjectNormalizer())->setIgnoredAttributes(array('building'))];

            $serializer = new Serializer($normalizers, $encoders);

            return $serializer;
        })());
    }

}
