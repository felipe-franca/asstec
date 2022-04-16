<?php

namespace App\Entity;

use App\Repository\PhoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhoneRepository::class)]
class Phone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 15)]
    private $number;

    #[ORM\OneToMany(mappedBy: 'phone', targetEntity: UserPhone::class)]
    private $userPhones;

    public function __construct()
    {
        $this->userPhones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return Collection<int, UserPhone>
     */
    public function getUserPhones(): Collection
    {
        return $this->userPhones;
    }

    public function addUserPhone(UserPhone $userPhone): self
    {
        if (!$this->userPhones->contains($userPhone)) {
            $this->userPhones[] = $userPhone;
            $userPhone->setPhone($this);
        }

        return $this;
    }

    public function removeUserPhone(UserPhone $userPhone): self
    {
        if ($this->userPhones->removeElement($userPhone)) {
            // set the owning side to null (unless already changed)
            if ($userPhone->getPhone() === $this) {
                $userPhone->setPhone(null);
            }
        }

        return $this;
    }
}
