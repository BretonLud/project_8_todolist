<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserService
{
    public function __construct(
        private UserRepository              $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }
    
    public function findAll(): array
    {
        return $this->userRepository->findAll();
    }
    
    public function find(User $user): ?User
    {
        return $this->userRepository->find($user->getId());
    }
    
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }
    
    public function remove(User $user): void
    {
        $this->userRepository->remove($user);
    }
    
    public function encoderPassword(User $user): void
    {
        $encodedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($encodedPassword);
    }
}