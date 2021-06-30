<?php

namespace App\Entity;

use App\Repository\OrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrganizationRepository::class)
 */
class Organization
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="organization")
     */
    private $participant;

    public function __construct() {
        $this->participant = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(string $title): self {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getParticipant(): Collection {
        return $this->participant;
    }

    public function addParticipant(User $participant): self {
        if (!$this->participant->contains($participant)) {
            $this->participant[] = $participant;
            $participant->setOrganization($this);
        }

        return $this;
    }

    public function removeParticipant(User $participant): self {
        if ($this->participant->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getOrganization() === $this) {
                $participant->setOrganization(null);
            }
        }

        return $this;
    }
}
