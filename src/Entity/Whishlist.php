<?php

namespace App\Entity;

use App\Repository\WhishlistRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WhishlistRepository::class)]
class Whishlist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'whishlists')]
    private ?User $idUser = null;

    #[ORM\Column]
    private array $idProducts = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $order_date = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $reception_date = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getIdProducts(): array
    {
        return $this->idProducts;
    }

    public function setIdProducts(array $idProducts): static
    {
        $this->idProducts = $idProducts;

        return $this;
    }

    public function getOrderDate(): ?\DateTimeImmutable
    {
        return $this->order_date;
    }

    public function setOrderDate(\DateTimeImmutable $order_date): static
    {
        $this->order_date = $order_date;

        return $this;
    }

    public function getReceptionDate(): ?\DateTimeImmutable
    {
        return $this->reception_date;
    }

    public function setReceptionDate(?\DateTimeImmutable $reception_date): static
    {
        $this->reception_date = $reception_date;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
