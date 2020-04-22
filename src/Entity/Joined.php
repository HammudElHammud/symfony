<?php

namespace App\Entity;

use App\Entity\Admin\trip;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JoinedRepository")
 */
class Joined
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Admin\trip")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $trip;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $added_at;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrip(): ?trip
    {
        return $this->trip;
    }

    public function setTrip(?trip $trip): self
    {
        $this->trip = $trip;

        return $this;
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

    public function getAddedAt(): ?\DateTimeInterface
    {
        return $this->added_at;
    }

    public function setAddedAt(?\DateTimeInterface $added_at): self
    {
        $this->added_at = $added_at;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
