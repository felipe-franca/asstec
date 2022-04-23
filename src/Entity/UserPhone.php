<?php

namespace App\Entity;

use App\Repository\UserPhoneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPhoneRepository::class)]
class UserPhone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userPhones')]
    private $user;

    #[ORM\ManyToOne(targetEntity: Phone::class, inversedBy: 'userPhones', fetch: "EAGER")]
    private $phone;

    #[ORM\ManyToOne(targetEntity: ClientUser::class, inversedBy: 'userPhones')]
    private $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPhone(): ?Phone
    {
        return $this->phone;
    }

    public function setPhone(?Phone $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getClient(): ?ClientUser
    {
        return $this->client;
    }

    public function setClient(?ClientUser $client): self
    {
        $this->client = $client;

        return $this;
    }
}
