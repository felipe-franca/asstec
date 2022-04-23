<?php

namespace App\Entity;

use App\Repository\ClientUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ClientUserRepository::class)]
class ClientUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 40)]
    private $username;

    #[ORM\OneToOne(targetEntity: Address::class, cascade: ['persist', 'remove'], fetch: "EAGER")]
    private $address;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: UserPhone::class, fetch: "EAGER")]
    private $userPhones;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Tickets::class)]
    private $tickets;

    public function __construct()
    {
        $this->userPhones = new ArrayCollection();
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

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
            $userPhone->setClient($this);
        }

        return $this;
    }

    public function removeUserPhone(UserPhone $userPhone): self
    {
        if ($this->userPhones->removeElement($userPhone)) {
            // set the owning side to null (unless already changed)
            if ($userPhone->getClient() === $this) {
                $userPhone->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tickets>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Tickets $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setClient($this);
        }

        return $this;
    }

    public function removeTicket(Tickets $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getClient() === $this) {
                $ticket->setClient(null);
            }
        }

        return $this;
    }

    public function getPhonesListString()
    {
        return implode(' - ' , $this->userPhones->map(function (UserPhone $phone) {
            return $phone->getPhone()->getNumber();
        })->toArray());
    }
}
