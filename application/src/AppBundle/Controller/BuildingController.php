<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Building;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class BuildingController extends BaseController
{

    /**
     * @Route("/building", name="buildings")
     * @Method("GET")
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository(Building::class);
        $data = $repository->findAll();

        return $this->makeJsonResponse($data);
    }

    /**
     * @Route("/building/{id}", name="buildings_find", requirements={"id": "\d+"})
     * @Method("GET")
     */
    public function findAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(Building::class);
        $data = $repository->find($id);
        if(is_null($data)) {
            return $this->makeJsonNotFoundResponse('Building not found');
        }

        return $this->makeJsonResponse($data, 200, [], false, (function () {
            $encoders = [new JsonEncoder()];
            $normalizers = [(new ObjectNormalizer())->setIgnoredAttributes(array('lifts'))];

            $serializer = new Serializer($normalizers, $encoders);

            return $serializer;
        })());
    }
}
