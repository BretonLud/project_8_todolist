<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: "App\Repository\TaskRepository")]
#[ORM\Table]
class Task
{
    #[ORM\Column(type: "integer")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private int $id;
    
    #[ORM\Column(type: "datetime")]
    private \DateTime $createdAt;
    
    #[ORM\Column(type: "string")]
    #[Assert\NotBlank(message: "Vous devez saisir un titre.")]
    #[Assert\Type('string')]
    private string $title = "";
    
    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "Vous devez saisir du contenu.")]
    #[Assert\Type('string')]
    private string $content = "";
    
    #[ORM\Column(type: "boolean")]
    #[Assert\Type('boolean')]
    private bool $isDone;
    
    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?User $user = null;
    
    public function __construct()
    {
        $this->createdAt = new \Datetime();
        $this->isDone = false;
    }
    
    public function getId(): int
    {
        return $this->id;
    }
    
    public function getCreatedAt(): \Datetime
    {
        return $this->createdAt;
    }
    
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    
    public function getTitle(): string
    {
        return $this->title;
    }
    
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }
    
    public function getContent(): string
    {
        return $this->content;
    }
    
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }
    
    public function isDone(): bool
    {
        return $this->isDone;
    }
    
    public function toggle(bool $flag): self
    {
        $this->isDone = $flag;
        return $this;
    }
    
    public function getUser(): ?User
    {
        return $this->user;
    }
    
    public function setUser(?User $user): static
    {
        $this->user = $user;
        
        return $this;
    }
    
    public function __toString(): string
    {
        return $this->title;
    }
}
