<?php

namespace App\Entity;

use App\Repository\TokenRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TokenRepository::class)
 */
class Token
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $targetUser;

    /**
     * @ORM\Column(type="bigint")
     */
    private $exp;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $key;

    public function getId(): ?int {
        return $this->id;
    }

    public function getUser(): ?User {
        return $this->targetUser;
    }

    public function setUser(User $targetUser): self {
        $this->targetUser = $targetUser;

        return $this;
    }

    public function getExp(): ?string {
        return $this->exp;
    }

    public function setExp(string $exp): self {
        $this->exp = $exp;

        return $this;
    }

    public function getKey(): ?string {
        return $this->key;
    }

    public function setKey(string $key): self {
        $this->key = $key;

        return $this;
    }
}
