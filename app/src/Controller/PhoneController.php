<?php

namespace App\Controller;

use App\Service\PhoneService;
use Symfony\Component\HttpFoundation\Response;

class PhoneController extends AbstractApiController
{

    public function indexAction(PhoneService $phoneService): Response
    {
        $result = $phoneService->index();

        return $this->respond($result);
    }

    public function createAction(PhoneService $phoneService): Response
    {
        $result = $phoneService->create();

        return $this->respond($result);
    }

    public function deleteAction(PhoneService $phoneService): Response
    {
        $result = $phoneService->delete();

        return $this->respond($result);
    }

    public function updateAction(PhoneService $phoneService): Response
    {
        $result = $phoneService->update();

        return $this->respond($result);
    }
}
