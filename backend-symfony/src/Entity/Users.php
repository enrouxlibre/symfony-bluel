<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    /**
     * @var Collection<int, Works>
     */
    #[ORM\OneToMany(targetEntity: Works::class, mappedBy: 'userId', orphanRemoval: true)]
    private Collection $works;

    public function __construct()
    {
        $this->works = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, Works>
     */
    public function getWorks(): Collection
    {
        return $this->works;
    }

    public function addWork(Works $work): static
    {
        if (!$this->works->contains($work)) {
            $this->works->add($work);
            $work->setUserId($this);
        }

        return $this;
    }

    public function removeWork(Works $work): static
    {
        if ($this->works->removeElement($work)) {
            // set the owning side to null (unless already changed)
            if ($work->getUserId() === $this) {
                $work->setUserId(null);
            }
        }

        return $this;
    }
}
