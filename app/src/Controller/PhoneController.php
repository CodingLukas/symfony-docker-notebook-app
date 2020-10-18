<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Form\Type\PhoneType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class PhoneController extends AbstractApiController
{

    public function indexAction()
    {
        $phones = $this->getDoctrine()->getRepository(Phone::class)->findBy(['user' => $this->getUser()]);

        if (!$phones) {
            throw new NotFoundHttpException('Phone book is empty');
        }

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $jsonContent = $serializer->normalize($phones, 'json', [AbstractNormalizer::ATTRIBUTES => ['id', 'name', 'number']]);

        $data = [
            'phone_book' => $jsonContent,
        ];

        return $this->respond($data);
    }

    public function createAction(Request $request): Response
    {

        $form = $this->buildForm(PhoneType::class);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /** @var Phone $phone */
        $phone = $form->getData();
        $phone->setUser($this->getUser());

        $this->getDoctrine()->getManager()->persist($phone);
        $this->getDoctrine()->getManager()->flush();

        return $this->respond($phone);
    }

    public function deleteAction(Request $request): Response
    {
        $phoneId = $request->get('id');

        if (!$phoneId) {
            return $this->respond(['message' => 'id parameter is not provided'], Response::HTTP_BAD_REQUEST);
        }

        $phone = $this->getDoctrine()->getRepository(Phone::class)->findOneBy([
            'user' => $this->getUser(),
            'id' => $phoneId,
        ]);

        if (!$phone) {
            return $this->respond(['message' => 'Phone not found'], Response::HTTP_BAD_REQUEST);
        }

        $this->getDoctrine()->getManager()->remove($phone);
        $this->getDoctrine()->getManager()->flush();

        return $this->respond([null]);
    }

    public function updateAction(Request $request): Response
    {
        $phoneId = $request->get('id');

        if (!$phoneId) {
            return $this->respond(['message' => 'id parameter is not provided'], Response::HTTP_BAD_REQUEST);
        }

        $phone = $this->getDoctrine()->getRepository(Phone::class)->findOneBy([
            'user' => $this->getUser(),
            'id' => $phoneId,
        ]);

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

        $this->getDoctrine()->getManager()->persist($phone);
        $this->getDoctrine()->getManager()->flush();

        return $this->respond($phone);
    }

}
