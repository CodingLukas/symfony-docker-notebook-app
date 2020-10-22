<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiController extends AbstractController
{

    protected function respond($data): Response
    {
        $statusCode = $data['status'];
        $object = [];

        if (isset($data[0])) {
            $object = $data[0];
        }

        if (isset($data['message'])) {
            $object = ['message' =>  $data['message']];
        }

        return $this->json($object, $statusCode);
    }
}
