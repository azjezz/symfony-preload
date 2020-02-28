<?php

namespace App\Entity;

use Psl\Arr;
use Psl\Str;
use Psl\Iter;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"})
 */
class User implements UserInterface
{
    public const RoleDefault = 'ROLE_USER';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?string $id = null;

    /**
     * @Assert\Length(min="4", max="32")
     * @Assert\Regex(pattern="/^[a-zA-Z0-9_]+$/i", htmlPattern="^[a-zA-Z0-9_]+$")
     *
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private ?string $username = null;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string")
     */
    private ?string $password = null;

    /**
     * @Assert\Length(min="8", max="4096", minMessage="Your password should be at least {{ limit }} characters")
     * @Assert\NotCompromisedPassword
     */
    private ?string $plainPassword = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Status", mappedBy="user", orphanRemoval=true)
     */
    private Collection $statuses;

    public function __construct()
    {
        $this->statuses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get all user roles.
     *
     * @return array<int, string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = self::RoleDefault;

        return Arr\unique(
            Iter\map($roles, fn(string $role): string => Str\uppercase($role))
        );
    }

    /**
     * Set all user roles.
     *
     * @param iterable<int, string> $roles
     */
    public function setRoles(iterable $roles): void
    {
        $roles[] = self::RoleDefault;

        $this->roles = Arr\unique(
            Iter\map($roles, fn(string $role): string => Str\uppercase($role))
        );
    }

    public function addRole(string $role): void
    {
        if ($this->hasRole($role)) {
            return;
        }

        $roles = $this->getRoles();
        $roles[] = Str\uppercase($role);
        $this->setRoles($roles);
    }

    public function hasRole(string $role): bool
    {
        $roles = $this->getRoles();
        $role = Str\uppercase($role);

        return Arr\contains($roles, $role);
    }

    public function removeRole(string $role): void
    {
        if (!$this->hasRole($role)) {
            return;
        }

        $roles = $this->getRoles();
        $role = Str\uppercase($role);
        $this->setRoles(Iter\filter($roles, fn(string $value): bool => $value !== $role));
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getStatuses(): Collection
    {
        return $this->statuses;
    }

    public function addStatus(Status $status): self
    {
        if (!$this->statuses->contains($status)) {
            $this->statuses[] = $status;
            $status->setUser($this);
        }

        return $this;
    }

    public function removeStatus(Status $status): self
    {
        if ($this->statuses->contains($status)) {
            $this->statuses->removeElement($status);
            // set the owning side to null (unless already changed)
            if ($status->getUser() === $this) {
                $status->setUser(null);
            }
        }

        return $this;
    }
}
