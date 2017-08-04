<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class BaseController extends Controller
{

    protected $serializer;

    public function __construct()
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @param mixed            $data       The response data
     * @param int              $status     The response status code
     * @param array            $headers    An array of response headers
     * @param bool             $json       If the data is already a JSON string
     * @param Serializer|null  $serializer
     * @return JsonResponse
     */
    protected function makeJsonResponse($data = null, $status = 200, $headers = [], $json = false, $serializer = null)
    {
        if(is_null($serializer)) {
            $serializer = $this->serializer;
        }

        if(!is_string($data)) {
            $data = $serializer->serialize($data, 'json');
            $json = true;
        }

        if(!$json) {
            $data = $serializer->serialize($data, 'json');
        }

        return new JsonResponse($data, $status, $headers, true);
    }

    protected function makeJsonErrorResponse($message, $status = 400, $code = -1, $additional_data = [])
    {
        return new JsonResponse(array_merge([
            'error' => $message === '' ? 'Bad request' : $message,
            'error_code' => $code
        ], $additional_data), $status, [], false);
    }

    protected function makeJsonNotFoundResponse($message = '')
    {
        return $this->makeJsonErrorResponse($message === '' ? 'Not found' : $message, 404, 1);
    }
}
