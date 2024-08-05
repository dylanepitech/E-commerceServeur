<?php

namespace App\Entity;

use App\Repository\ProductsCommentsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductsCommentsRepository::class)]
class ProductsComments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'productsComments')]
    private ?products $idProducts = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'productsComments')]
    private ?user $idUser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdProducts(): ?products
    {
        return $this->idProducts;
    }

    public function setIdProducts(?products $idProducts): static
    {
        $this->idProducts = $idProducts;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getIdUser(): ?user
    {
        return $this->idUser;
    }

    public function setIdUser(?user $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }
}
