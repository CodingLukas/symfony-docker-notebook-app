<?php

namespace App\Service;

use App\Entity\Phone;
use App\Form\Type\PhoneType;
use App\Repository\PhoneRepository;
use App\Repository\SharedPhoneRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class PhoneService extends AbstractApiService
{
    private $phoneRepository;
    private $sharedPhoneRepository;
    protected $requestStack;
    private $security;

    public function __construct(
        PhoneRepository $phoneRepository,
        SharedPhoneRepository $sharedPhoneRepository,
        RequestStack $requestStack,
        Security $security
    ) {
        $this->requestStack = $requestStack;
        $this->phoneRepository = $phoneRepository;
        $this->sharedPhoneRepository = $sharedPhoneRepository;
        $this->security = $security;
        parent::__construct();
    }

    public function index()
    {
        $phones = $this->phoneRepository->findAllByUser($this->security->getUser());
        $sharedPhones = $this->sharedPhoneRepository->findAllByUser($this->security->getUser());

        $attributes = ['id', 'name', 'number'];
        $jsonContent = $this->prepareJSON($phones, $attributes);
        $attributes = ['id', 'from_user', 'to_user', 'phone' => ['id', 'name', 'number']];
        $jsonContent2 = $this->prepareJSON($sharedPhones, $attributes);

        $data = [
            'phone_book' => $jsonContent,
            'shared_phones' => $jsonContent2
        ];

        return [$data, 'status' => Response::HTTP_OK];
    }

    public function create()
    {
        $form = $this->buildForm(PhoneType::class);

        $request = $this->requestStack->getCurrentRequest();

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return ['message' => 'Submitted data is incorrect', 'status' => Response::HTTP_BAD_REQUEST];
        }

        /** @var Phone $phone */
        $phone = $form->getData();
        $phone->setUser($this->security->getUser());

        $success = $this->phoneRepository->create($phone);

        if ($success) {
            return ['status' => Response::HTTP_CREATED];
        }

        return ['message' => 'Database: failed on create', 'status' => Response::HTTP_INTERNAL_SERVER_ERROR];
    }

    public function update()
    {
        $result = $this->getPhoneById();

        if (is_array($result)) {
            return $result;
        }

        $phone = $result;

        $request = $this->requestStack->getCurrentRequest();

        $form = $this->buildForm(PhoneType::class, $phone, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return ['message' => 'Submitted data is incorrect', 'status' => Response::HTTP_BAD_REQUEST];
        }

        /** @var Phone $phone */
        $phone = $form->getData();

        $success = $this->phoneRepository->update($phone);

        if ($success) {
            return ['status' => Response::HTTP_NO_CONTENT];
        }

        return ['message' => 'Database: failed on update', 'status' => Response::HTTP_INTERNAL_SERVER_ERROR];
    }

    public function delete()
    {
        $result = $this->getPhoneById();

        if (is_array($result)) {
            return $result;
        }

        $phone = $result;

        $success = $this->phoneRepository->delete($phone);

        if ($success) {
            return ['message' => '', 'status' => Response::HTTP_NO_CONTENT];
        }

        return ['message' => 'Database: failed on delete', 'status' => Response::HTTP_INTERNAL_SERVER_ERROR];
    }

    private function getPhoneById()
    {
        $phoneId = $this->requestStack->getCurrentRequest()->get('id');

        if (!$phoneId) {
            return ['message' => 'id parameter is not provided', 'status' => Response::HTTP_BAD_REQUEST];
        }

        $data = [
            'user' => $this->security->getUser(),
            'id' => $phoneId,
        ];

        $phone = $this->phoneRepository->findOneByData($data);

        if (!$phone) {
            return ['message' => 'Phone not found', 'status' => Response::HTTP_BAD_REQUEST];
        }

        return $phone;
    }
}
