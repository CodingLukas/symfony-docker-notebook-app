<?php

namespace App\Controller;

use App\Entity\SharedPhone;
use App\Form\Type\SharedPhoneType;
use App\Repository\PhoneRepository;
use App\Repository\SharedPhoneRepository;
use App\Repository\UserRepository;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SharedPhoneController extends AbstractApiController
{

    public function shareAction(Request $request, PhoneRepository $phoneRepository, SharedPhoneRepository $sharePhoneRepository, UserRepository $userRepository): Response
    {
        $phoneId = $request->get('phone');

        if (!$phoneId) {
            return $this->respond(['message' => 'phone parameter is not provided'], Response::HTTP_BAD_REQUEST);
        }

        $data = [
            'user' => $this->getUser(),
            'id' => $phoneId,
        ];

        $phone = $phoneRepository->findOneByData($data);

        if (!$phone) {
            return $this->respond(['message' => 'Phone not found'], Response::HTTP_BAD_REQUEST);
        }

        $toUserId = $request->get('to_user');

        if (!$toUserId) {
            return $this->respond(['message' => 'to_user parameter is not provided'], Response::HTTP_BAD_REQUEST);
        }

        $toUser = $userRepository->findOneById($toUserId);

        if (!$toUser) {
            return $this->respond(['message' => 'to_user does not exist'], Response::HTTP_BAD_REQUEST);
        }

        if ($toUser->getId() == $this->getUser()->getId()) {
            return $this->respond(['message' => 'you can not share with yourself'], Response::HTTP_BAD_REQUEST);
        }

        $data = [
            'from_user' => $this->getUser(),
            'to_user' => $toUser,
            'phone' => $phone,
        ];

        $sharePhone = $sharePhoneRepository->findOneByData($data);

        if ($sharePhone) {
            return $this->respond(['message' => 'you have already shared this phone'], Response::HTTP_BAD_REQUEST);
        }

        $form = $this->buildForm(SharedPhoneType::class);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /** @var SharedPhone $sharePhone */
        $sharePhone = $form->getData();
        $sharePhone->setFromUser($this->getUser());

        $sharePhoneRepository->create($sharePhone);

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $jsonContent = $serializer->normalize($phone, 'json', [AbstractNormalizer::ATTRIBUTES => ['id', 'name', 'number']]);

        $data = [
            'phone' => $jsonContent,
        ];

        return $this->respond($data);
    }

    public function shareCancelAction(Request $request, SharedPhoneRepository $sharePhoneRepository): Response
    {
        $sharedPhoneId = $request->get('id');

        if (!$sharedPhoneId) {
            return $this->respond(['message' => 'id parameter is not provided'], Response::HTTP_BAD_REQUEST);
        }

        $data = [
            'to_user' => $this->getUser(),
            'id' => $sharedPhoneId
        ];

        $phone = $sharePhoneRepository->findOneByData($data);

        if (!$phone) {
            return $this->respond(['message' => 'SharedPhone not found'], Response::HTTP_BAD_REQUEST);
        }

        $sharePhoneRepository->delete($phone);

        return $this->respond([]);
    }

    public function shareIndexAction(SharedPhoneRepository $sharePhoneRepository)
    {
        $sharedPhones = $sharePhoneRepository->findAllByUser($this->getUser());

        if (!$sharedPhones) {
            return $this->respond(['message' => 'Shared phone book is empty'], Response::HTTP_BAD_REQUEST);
        }

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $jsonContent = $serializer->normalize($sharedPhones, 'json', [AbstractNormalizer::ATTRIBUTES => ['id', 'phone' => ['id', 'name', 'number']]]);

        $data = [
            'shared_phones' => $jsonContent,
        ];

        return $this->respond($data);
    }

}
