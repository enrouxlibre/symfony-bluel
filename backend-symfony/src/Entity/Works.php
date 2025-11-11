<?php

namespace App\Entity;

use App\Repository\WorksRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorksRepository::class)]
class Works
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $imageUrl = null;

    #[ORM\ManyToOne(inversedBy: 'works')]
    #[ORM\JoinColumn(nullable: false,name:"categoryId")]
    private ?Categories $category = null;

    #[ORM\ManyToOne(inversedBy: 'works')]
    #[ORM\JoinColumn(nullable: false,name:"userId")]
    private ?Users $author = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getauthor(): ?Users
    {
        return $this->author;
    }

    public function setauthor(?Users $author): static
    {
        $this->author = $author;

        return $this;
    }
}
