<?php
 
namespace App\Entity;
 
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
 
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
 
    #[ORM\Column(length: 180)]
    private ?string $email = null;
 
    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];
 
    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;
 
    #[ORM\Column(length: 255)]
    private ?string $firstname = null;
 
    #[ORM\Column(length: 255)]
    private ?string $lastname = null;
 
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;
 
    #[ORM\Column]
    private ?bool $is_verified = null;
 
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $picture = null;
 
    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;
 
    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;
 
 
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
 
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }
 
    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
 
        return array_unique($roles);
    }
 
    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
 
        return $this;
    }
 
    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }
 
    public function setPassword(string $password): static
    {
        $this->password = $password;
 
        return $this;
    }
 
    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
 
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }
 
    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;
 
        return $this;
    }
 
    public function getLastname(): ?string
    {
        return $this->lastname;
    }
 
    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;
 
        return $this;
    }
 
    public function getToken(): ?string
    {
        return $this->token;
    }
 
    public function setToken(string $token): static
    {
        $this->token = $token;
 
        return $this;
    }
 
    public function isVerified(): ?bool
    {
        return $this->is_verified;
    }
 
    public function setVerified(bool $is_verified): static
    {
        $this->is_verified = $is_verified;
 
        return $this;
    }
 
    public function getPicture(): ?string
    {
        return $this->picture;
    }
 
    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;
 
        return $this;
    }
 
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }
 
    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;
 
        return $this;
    }
 
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }
 
    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;
 
        return $this;
    }
 
 
 
}