<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="shared_phone")
 * @ORM\Entity()
 */
class SharedPhone
{
    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Phone|null
     *
     * @ORM\ManyToOne(targetEntity="Phone")
     */
    private $phone;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $from_user;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $to_user;


    /**
     * @return Phone|null
     */
    public function getPhone(): ?Phone
    {
        return $this->phone;
    }

    /**
     * @param Phone|null $phone
     */
    public function setPhone(?Phone $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return User|null
     */
    public function getFromUser(): ?User
    {
        return $this->from_user;
    }

    /**
     * @param User|null $user
     */
    public function setFromUser(?User $user): void
    {
        $this->from_user = $user;
    }

    /**
     * @return User|null
     */
    public function getToUser(): ?User
    {
        return $this->to_user;
    }

    /**
     * @param User|null $user
     */
    public function setToUser(?User $user): void
    {
        $this->to_user = $user;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

}