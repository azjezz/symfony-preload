<?php

namespace App\Entity;

use Psl;
use Psl\Str;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     collectionOperations={
 *         "get",
 *         "post"={"security"="is_granted('ROLE_USER')"},
 *     },
 *     itemOperations={
 *         "get"={
 *             "controller"=NotFoundAction::class,
 *             "read"=false,
 *             "output"=false,
 *         },
 *         "delete"={"security"="is_granted('ROLE_ADMIN') or object.user == user"},
 *     },
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\StatusRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Status
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private ?string $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="statuses")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $user = null;

    /**
     * @ORM\Column(type="text")
     * @Groups({"read", "write"})
     *
     * @Assert\NotBlank()
     */
    private ?string $text = null;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=true)
     * @Groups({"read"})
     */
    public ?DateTime $createdAt = null;

    public function __construct(?User $user = null)
    {
        $this->user = $user;
    }

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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Set createdAt timestamp.
     *
     * @ORM\PrePersist
     */
    public function setTimestamp(): void
    {
        if (null === $this->createdAt) {
            // Create a datetime with microseconds
            $dateTime = DateTime::createFromFormat('U.u', Str\format('%.6F', microtime(true)));

            Psl\invariant($dateTime instanceof DateTime, 'Unable to create a datetime object with microseconds.');
            $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));

            $this->createdAt = $dateTime;
        }
    }
}
