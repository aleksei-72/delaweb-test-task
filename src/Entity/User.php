<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\ErrorList;

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

        if (!preg_match('/^[а-яА-Я]{1,25}$/u', $firstName)) {
            throw new \Exception(ErrorList::E_INVALID_FIRST_NAME, 400);
        }

        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self {

        if (!preg_match('/^[а-яА-Я]{1,25}$/u', $lastName)) {
            throw new \Exception(ErrorList::E_INVALID_LAST_NAME, 400);
        }

        $this->lastName = $lastName;

        return $this;
    }

    public function getPhone(): ?string {
        return $this->phone;
    }

    public function setPhone(string $phone): self {

        if (!preg_match('/^[0-9]{6,15}$/', $phone)) {
            throw new \Exception(ErrorList::E_INVALID_PHONE, 400);
        }

        $this->phone = $phone;

        return $this;
    }

    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->password);
    }

    public function setPassword(string $password): self {

        if (!preg_match('/[0-9a-zA-Z!@#$%^&*]{6,}/', $password)) {
            throw new \Exception(ErrorList::E_INVALID_PASSWORD, 400);
        }

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

    /**
     * @param $doctrine
     * @param $json
     * @throws \Exception
     */
    public function patch($doctrine, $json) {

        $manager = $doctrine->getManager();


        if (!empty($json['first_name'])) {
            $this->setFirstName($json['first_name']);
        }

        if (!empty($json['last_name'])) {
            $this->setLastName($json['last_name']);
        }

        if (!empty($json['phone'])) {

            $userWithThisPhone = $doctrine->getRepository(User::class)->findOneBy(['phone' => $json['phone']]);

            if ($userWithThisPhone && $userWithThisPhone->getId() !== $this->getId()) {
                throw new \Exception(ErrorList::E_NOT_UNIQUE_PHONE, 400);
            }

            $this->setPhone($json['phone']);
        }

        if (!empty($json['organization'])) {

            $organization = $doctrine->getRepository(Organization::class)
                ->findOneBy(['title' => $json['organization']]);

            if (!$organization) {
                //create new Organization
                $organization = new Organization();
                $organization->setTitle($json['organization']);


                $manager->persist($organization);
            }
            $this->setOrganization($organization);
        }

        if (!empty($json['password'])) {
            $this->setPassword($json['password']);
        }

        if (!empty($json['invitatory_id'])) {

            $invitatoryUser = $doctrine->getRepository(User::class)->find($json['invitatory_id']);

            if (!$invitatoryUser) {
                throw new \Exception(ErrorList::E_INVALID_INVITATORY_ID, 400);
            }

            $this->setInvitatory($invitatoryUser);
        }

        $manager->flush();
    }

}
