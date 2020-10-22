<?php

namespace App\Repository;

use App\Entity\Phone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

class PhoneRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Phone::class);
    }

    public function create($phone)
    {
        try {
            $this->_em->persist($phone);
            $this->_em->flush();
        } catch (ORMException $e) {
            return false;
        }

        return true;
    }

    public function delete($phone)
    {
        try {
            $this->_em->remove($phone);
            $this->_em->flush();
        } catch (ORMException $e) {
            return false;
        }

        return true;
    }

    public function update($phone)
    {
        try {
            $this->_em->persist($phone);
            $this->_em->flush();
        } catch (ORMException $e) {
            return false;
        }

        return true;
    }

    public function findAllByUser($user)
    {
        return $this->findBy(['user' => $user]);
    }

    public function findOneByData($data)
    {
        return $this->findOneBy($data);
    }


}