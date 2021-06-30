<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="invitedUsers")
     * @ORM\JoinColumn(nullable=true)
     */
    private $invitatory;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="invitatory")
     */
    private $invitedUsers;

    /**
     * @ORM\ManyToOne(targetEntity=Organization::class, inversedBy="participant")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organization;


    public function __construct() {
        $this->organization = new ArrayCollection();
        $this->invitedUsers = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getFirstName(): ?string {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhone(): ?string {
        return $this->phone;
    }

    public function setPhone(string $phone): self {
        $this->phone = $phone;

        return $this;
    }

    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->password);
    }

    public function setPassword(string $password): self {
        $this->password = password_hash($password, PASSWORD_BCRYPT);

        return $this;
    }

    public function getInvitatory(): ?self {
        return $this->invitatory;
    }

    public function setInvitatory(?self $invitatory): self {
        $this->invitatory = $invitatory;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getInvitedUsers(): Collection {
        return $this->invitedUsers;
    }

    public function addInvitedUser(self $invitedUser): self {
        if (!$this->invitedUsers->contains($invitedUser)) {
            $this->invitedUsers[] = $invitedUser;
            $invitedUser->setInvitatory($this);
        }

        return $this;
    }

    public function removeInvitedUser(self $invitedUser): self {
        if ($this->invitedUsers->removeElement($invitedUser)) {
            // set the owning side to null (unless already changed)
            if ($invitedUser->getInvitatory() === $this) {
                $invitedUser->setInvitatory(null);
            }
        }

        return $this;
    }

    public function getOrganization(): ?Organization {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): self {
        $this->organization = $organization;

        return $this;
    }

    public function toArray(): array {
        return [
            'id' => $this->getId(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'phone' => $this->getPhone(),
            'invitatory_id' => $this->getInvitatory(),
            'organization' => $this->getOrganization()->toArray()
        ];
    }

}
