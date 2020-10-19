<?php

namespace App\Repository;

use App\Entity\Phone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PhoneRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Phone::class);
    }

    public function create($phone)
    {
        $this->_em->persist($phone);
        $this->_em->flush();
    }

    public function delete($phone)
    {
        $this->_em->remove($phone);
        $this->_em->flush();
    }

    public function findAllByUser($user)
    {
        return $this->findBy(['user' => $user]);
    }

    public function findOneByData($data)
    {
        return $this->findOneBy($data);
    }

    public function update($phone)
    {
        $this->_em->persist($phone);
        $this->_em->flush();
    }

}