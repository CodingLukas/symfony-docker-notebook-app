<?php

namespace App\Controller;

use App\Service\SharedPhoneService;
use Symfony\Component\HttpFoundation\Response;

class SharedPhoneController extends AbstractApiController
{

    public function shareAction(SharedPhoneService $sharedPhoneService): Response
    {
        $result = $sharedPhoneService->create();

        return $this->respond($result);
    }

    public function shareCancelAction(SharedPhoneService $sharedPhoneService): Response
    {
        $result =  $sharedPhoneService->delete();

        return $this->respond($result);
    }

    public function shareIndexAction(SharedPhoneService $sharedPhoneService): Response
    {
        $result = $sharedPhoneService->index();

        return $this->respond($result);
    }
}
