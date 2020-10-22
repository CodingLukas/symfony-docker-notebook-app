<?php

namespace App\Service;

use App\Entity\SharedPhone;
use App\Repository\PhoneRepository;
use App\Repository\SharedPhoneRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class SharedPhoneService extends AbstractApiService
{
    private $phoneRepository;
    private $sharedPhoneRepository;
    protected $requestStack;
    private $security;
    private $userRepository;

    public function __construct(
        PhoneRepository $phoneRepository,
        SharedPhoneRepository $sharedPhoneRepository,
        RequestStack $requestStack,
        Security $security,
        UserRepository $userRepository
    ) {
        $this->requestStack = $requestStack;
        $this->phoneRepository = $phoneRepository;
        $this->sharedPhoneRepository = $sharedPhoneRepository;
        $this->security = $security;
        $this->userRepository = $userRepository;
        parent::__construct();
    }

    public function index()
    {
        $sharedPhones = $this->sharedPhoneRepository->findAllByUser($this->security->getUser());

        $attributes = ['id', 'phone' => ['id', 'name', 'number']];
        $jsonContent = $this->prepareJSON($sharedPhones, $attributes);

        $data = [
            'shared_phones' => $jsonContent
        ];

        return [$data, 'status' => Response::HTTP_OK];
    }

    public function create()
    {
        $request = $this->requestStack->getCurrentRequest();

        $sharedPhoneId = $request->get('phone');

        if (!$sharedPhoneId) {
            return ['message' => 'phone parameter is not provided', 'status' => Response::HTTP_BAD_REQUEST];
        }

        $data = [
            'user' => $this->security->getUser(),
            'id' => $sharedPhoneId,
        ];

        $phone = $this->phoneRepository->findOneByData($data);

        if (!$phone) {
            return ['message' => 'Phone not found', 'status' => Response::HTTP_BAD_REQUEST];
        }

        $toUserId = $request->get('to_user');

        if (!$toUserId) {
            return ['message' => 'to_user parameter is not provided', 'status' => Response::HTTP_BAD_REQUEST];
        }

        $toUser = $this->userRepository->findOneById($toUserId);

        if (!$toUser) {
            return ['message' => 'to_user does not exist', 'status' => Response::HTTP_BAD_REQUEST];
        }

        if ($toUser->getId() == $this->security->getUser()->getId()) {
            return ['message' => 'you can not share with yourself', 'status' => Response::HTTP_BAD_REQUEST];
        }

        $data = [
            'from_user' => $this->security->getUser(),
            'to_user' => $toUser,
            'phone' => $phone,
        ];

        $sharePhone = $this->sharedPhoneRepository->findOneByData($data);

        if ($sharePhone) {
            return ['message' => 'you have already shared this phone', 'status' => Response::HTTP_BAD_REQUEST];
        }

        $sharePhone = new SharedPhone();

        $sharePhone->setFromUser($this->security->getUser());
        $sharePhone->setToUser($toUser);
        $sharePhone->setPhone($phone);

        $success = $this->sharedPhoneRepository->create($sharePhone);

        if ($success) {
            return ['status' => Response::HTTP_CREATED];
        }

        return ['message' => 'Database: failed on create', 'status' => Response::HTTP_INTERNAL_SERVER_ERROR];
    }

    public function delete()
    {
        $result = $this->getSharedPhoneById();

        if (is_array($result)) {
            return $result;
        }

        $sharedPhone = $result;

        $success = $this->sharedPhoneRepository->delete($sharedPhone);

        if ($success) {
            return ['message' => '', 'status' => Response::HTTP_NO_CONTENT];
        }

        return ['message' => 'Database: failed on delete', 'status' => Response::HTTP_INTERNAL_SERVER_ERROR];
    }

    private function getSharedPhoneById()
    {
        $sharedPhoneId = $this->requestStack->getCurrentRequest()->get('id');

        if (!$sharedPhoneId) {
            return ['message' => 'id parameter is not provided', 'status' => Response::HTTP_BAD_REQUEST];
        }

        $data = [
            'from_user' => $this->security->getUser(),
            'id' => $sharedPhoneId,
        ];

        $sharedPhone = $this->sharedPhoneRepository->findOneByData($data);

        if (!$sharedPhone) {
            return ['message' => 'Phone not found', 'status' => Response::HTTP_BAD_REQUEST];
        }

        return $sharedPhone;
    }
}
