<?php

namespace App\Entity;

use App\Entity\Admin\trip;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $commented_by;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Admin\trip")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $on_trip;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $commented_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subject;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rating;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $ip;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCommentedBy(): ?User
    {
        return $this->commented_by;
    }

    public function setCommentedBy(?User $commented_by): self
    {
        $this->commented_by = $commented_by;

        return $this;
    }

    public function getOnTrip(): ?trip
    {
        return $this->on_trip;
    }

    public function setOnTrip(?trip $on_trip): self
    {
        $this->on_trip = $on_trip;

        return $this;
    }

    public function getCommentedAt(): ?\DateTimeInterface
    {
        return $this->commented_at;
    }

    public function setCommentedAt(?\DateTimeInterface $commented_at): self
    {
        $this->commented_at = $commented_at;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
