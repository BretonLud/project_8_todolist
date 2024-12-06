<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "user")]
#[ORM\Entity(repositoryClass: "App\Repository\UserRepository")]
#[UniqueEntity("username", "Il existe déjà une personne avec cet username.")]
#[UniqueEntity("email", "Il existe déjà une personne avec cet email.")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;
    
    #[ORM\Column(type: "string", length: 25, unique: true)]
    #[Assert\NotBlank(message: "Vous devez saisir un nom d'utilisateur.")]
    #[Assert\Type('string')]
    private string $username = "";
    
    #[ORM\Column(type: "string", length: 64)]
    #[Assert\NotBlank(message: "Vous devez saisir un mot de passe.")]
    #[Assert\Type('string')]
    private string $password;
    
    #[ORM\Column(type: "string", length: 60, unique: true)]
    #[Assert\NotBlank(message: "Vous devez saisir une adresse email.")]
    #[Assert\Email(message: "Le format de l'adresse n'est pas correcte.")]
    #[Assert\Type('string')]
    private string $email;
    
    #[ORM\Column]
    private array $roles = ['ROLE_USER'];
    
    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'user')]
    private Collection $tasks;
    
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }
    
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }
    
    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }
    
    public function getUserIdentifier(): string
    {
        return $this->username;
    }
    
    public function getPassword(): string
    {
        return $this->password;
    }
    
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }
    
    public function getEmail(): string
    {
        return $this->email;
    }
    
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    
    public function eraseCredentials(): void
    {
    }
    
    public function __toString()
    {
        return $this->username;
    }
    
    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }
    
    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setUser($this);
        }
        
        return $this;
    }
    
    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getUser() === $this) {
                $task->setUser(null);
            }
        }
        
        return $this;
    }
    
    /**
     * @return list<string>
     * @see UserInterface
     *
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        
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
}
