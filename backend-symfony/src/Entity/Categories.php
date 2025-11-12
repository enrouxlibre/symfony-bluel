<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Works>
     */
    #[ORM\OneToMany(targetEntity: Works::class, mappedBy: 'category', orphanRemoval: true)]
    #[Ignore]
    private Collection $works;

    public function __construct()
    {
        $this->works = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
            $work->setCategory($this);
        }

        return $this;
    }

    public function removeWork(Works $work): static
    {
        if ($this->works->removeElement($work)) {
            // set the owning side to null (unless already changed)
            if ($work->getCategory() === $this) {
                $work->setCategory(null);
            }
        }

        return $this;
    }
}
