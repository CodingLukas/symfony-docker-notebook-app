<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Form\Type\PhoneType;
use App\Repository\PhoneRepository;
use App\Repository\SharePhoneRepository;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class PhoneController extends AbstractApiController
{

    public function indexAction(PhoneRepository $phoneRepository, SharePhoneRepository $sharePhoneRepository)
    {
        $phones = $phoneRepository->findAllByUser($this->getUser());
        $sharedPhones = $sharePhoneRepository->findAllByUser($this->getUser());

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $jsonContent = $serializer->normalize($phones, 'json', [AbstractNormalizer::ATTRIBUTES => ['id', 'name', 'number']]);
        $jsonContent2 = $serializer->normalize($sharedPhones, 'json',
            [AbstractNormalizer::ATTRIBUTES => ['id', 'from_user', 'to_user', 'phone' => ['id', 'name', 'number']]]);

        $data = [
            'phone_book' => $jsonContent,
            'shared_phones' => $jsonContent2
        ];

        return $this->respond($data);
    }

    public function createAction(Request $request, PhoneRepository $phoneRepository): Response
    {
        $form = $this->buildForm(PhoneType::class);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /** @var Phone $phone */
        $phone = $form->getData();
        $phone->setUser($this->getUser());

        $phoneRepository->create($phone);

        return $this->respond($phone);
    }

    public function deleteAction(Request $request, PhoneRepository $phoneRepository): Response
    {
        $phoneId = $request->get('id');

        if (!$phoneId) {
            return $this->respond(['message' => 'id parameter is not provided'], Response::HTTP_BAD_REQUEST);
        }

        $data = [
            'user' => $this->getUser(),
            'id' => $phoneId,
        ];

        $phone = $phoneRepository->findOneByData($data);

        if (!$phone) {
            return $this->respond(['message' => 'Phone not found'], Response::HTTP_BAD_REQUEST);
        }

        $phoneRepository->delete($phone);

        return $this->respond([]);
    }

    public function updateAction(Request $request, PhoneRepository $phoneRepository): Response
    {
        $phoneId = $request->get('id');

        if (!$phoneId) {
            return $this->respond(['message' => 'id parameter is not provided'], Response::HTTP_BAD_REQUEST);
        }

        $data = [
            'user' => $this->getUser(),
            'id' => $phoneId,
        ];

        $phone = $phoneRepository->findOneByData($data);

        if (!$phone) {
            return $this->respond(['message' => 'Phone not found'], Response::HTTP_BAD_REQUEST);
        }

        $form = $this->buildForm(PhoneType::class, $phone, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /** @var Phone $phone */
        $phone = $form->getData();

        $phoneRepository->update($phone);

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $jsonContent = $serializer->normalize($phone, 'json', [AbstractNormalizer::ATTRIBUTES => ['id', 'name', 'number']]);

        $data = [
            'phone' => $jsonContent,
        ];

        return $this->respond($data);
    }

}
