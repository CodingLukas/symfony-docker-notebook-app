<?php

namespace App\Repository;

use App\Entity\SharedPhone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

class SharedPhoneRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SharedPhone::class);
    }

    public function create($sharedPhone)
    {
        try {
            $this->_em->persist($sharedPhone);
            $this->_em->flush();
        } catch (ORMException $e) {
            return false;
        }

        return true;
    }

    public function delete($sharedPhone)
    {
        try {
            $this->_em->remove($sharedPhone);
            $this->_em->flush();
        } catch (ORMException $e) {
            return false;
        }

        return true;
    }

    public function update($sharedPhone)
    {
        try {
            $this->_em->persist($sharedPhone);
            $this->_em->flush();
        } catch (ORMException $e) {
            return false;
        }

        return true;
    }

    public function findAllByUser($user)
    {
        return $this->findBy(['to_user' => $user]);
    }

    public function findOneByData($data)
    {
        return $this->findOneBy($data);
    }
}
