<?php

namespace App\Entity;

use App\Repository\TicketsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketsRepository::class)]
class Tickets
{

    const STATUS_APPROVAL_PENDING = 'approval_pending';
    const STATUS_OPENED           = 'opened';
    const STATUS_ACTING           = 'acting';
    const STATUS_FINISHED         = 'finished';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $ticketNumber;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $closedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $serviceStart;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $serviceEnd;

    #[ORM\Column(type: 'text')]
    private $reason;

    #[ORM\Column(type: 'text', nullable: true)]
    private $observation;

    #[ORM\Column(type: 'text', nullable: true)]
    private $solution;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tickets')]
    private $responsable;

    #[ORM\ManyToOne(targetEntity: ClientUser::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private $client;

    #[ORM\Column(type: 'string', length: 30)]
    private $status;

    public function __construct()
    {
        $now = new \DateTime('now');
        $this->setTicketNumber(date('YmdHis'));
        $this->setCreatedAt($now);
        $this->setStatus(self::STATUS_OPENED);
    }


    public static $statuses = [
        self::STATUS_ACTING           => 'Analista Atuando',
        self::STATUS_OPENED           => 'Em Aberto',
        self::STATUS_FINISHED         => 'Fechados',
        self::STATUS_APPROVAL_PENDING => 'Aguardando Aprovação'
    ];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getTicketNumber(): ?string
    {
        return $this->ticketNumber;
    }

    public function setTicketNumber(string $ticketNumber): self
    {
        $this->ticketNumber = $ticketNumber;

        return $this;
    }

    public function getClosedAt(): ?\DateTime
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTime $closedAt): self
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function getServiceStart(): ?\DateTime
    {
        return $this->serviceStart;
    }

    public function setServiceStart(?\DateTime $serviceStart): self
    {
        $this->serviceStart = $serviceStart;

        return $this;
    }

    public function getServiceEnd(): ?\DateTimeInterface
    {
        return $this->serviceEnd;
    }

    public function setServiceEnd(?\DateTimeInterface $serviceEnd): self
    {
        $this->serviceEnd = $serviceEnd;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): self
    {
        $this->observation = $observation;

        return $this;
    }

    public function getSolution(): ?string
    {
        return $this->solution;
    }

    public function setSolution(?string $solution): self
    {
        $this->solution = $solution;

        return $this;
    }

    public function getResponsable(): ?User
    {
        return $this->responsable;
    }

    public function setResponsable(?User $responsable): self
    {
        $this->responsable = $responsable;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatusLabel()
    {
        return Tickets::$statuses[$this->getStatus()];
    }
}
