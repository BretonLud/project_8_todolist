<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\ByteString;

readonly class UserService
{
    public function __construct(
        private UserRepository              $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private EmailService                $emailService,
    )
    {
    }
    
    public function findAll(): array
    {
        return $this->userRepository->findAll();
    }
    
    public function find(int $id): ?User
    {
        return $this->userRepository->find($id);
    }
    
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }
    
    public function encoderPassword(User $user): void
    {
        $encodedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($encodedPassword);
    }
    
    /**
     * @throws TransportExceptionInterface
     */
    public function sendPasswordMail(User $user): void
    {
        $this->emailService->sendMail($user, 'email/password_mail.html.twig', 'Votre mot de passe');
    }
    
    public function generatePassword(User $user): string
    {
        return $user->setPassword(ByteString::fromRandom(16, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=.'));
    }
}
