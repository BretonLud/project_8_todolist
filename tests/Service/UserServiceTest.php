<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\EmailService;
use App\Service\UserService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserServiceTest extends TestCase
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private EmailService $emailService;
    private UserService $userService;
    
    /**
     * @throws Exception
     */
    public function testFindAll(): void
    {
        $users = [$this->createMock(User::class)];
        $this->userRepository->method('findAll')->willReturn($users);
        
        $result = $this->userService->findAll();
        $this->assertSame($users, $result);
    }
    
    /**
     * @throws Exception
     */
    public function testSave(): void
    {
        $user = $this->createMock(User::class);
        $this->userRepository->expects($this->once())
            ->method('save')
            ->with($user);
        
        $this->userService->save($user);
    }
    
    /**
     * @throws Exception
     */
    public function testEncoderPassword(): void
    {
        $user = $this->createMock(User::class);
        $rawPassword = 'plain_password';
        $encodedPassword = 'encoded_password';
        
        $user->method('getPassword')->willReturn($rawPassword);
        
        $this->passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->with($user, $rawPassword)
            ->willReturn($encodedPassword);
        
        $user->expects($this->once())
            ->method('setPassword')
            ->with($encodedPassword);
        
        $this->userService->encoderPassword($user);
    }
    
    /**
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    public function testSendPasswordMail(): void
    {
        
        $user = $this->createMock(User::class);
        
        $this->emailService->expects($this->once())
            ->method('sendMail')
            ->with($user, 'email/password_mail.html.twig', 'Votre mot de passe');
        
        $this->userService->sendPasswordMail($user);
    }
    
    /**
     * @throws Exception
     */
    public function testGeneratePassword(): void
    {
        $user = new User();
        $this->userService->generatePassword($user);
        
        $this->assertIsString($user->getPassword());
        $this->assertEquals(16, strlen($user->getPassword()));
    }
    
    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->emailService = $this->createMock(EmailService::class);
        
        $this->userService = new UserService(
            $this->userRepository,
            $this->passwordHasher,
            $this->emailService
        );
    }
}