<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;

final class UserTest extends UserTestValidation
{
    public function testId(): void
    {
        $user = new User();
        $reflectionClass = new \ReflectionClass(User::class);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($user, 1);
        
        $this->assertEquals(1, $user->getId());
    }
    
    public function testUsername(): void
    {
        $user = new User();
        $user->setUsername('testuser');
        $this->assertEquals('testuser', $user->getUserIdentifier());
        $this->assertEquals('testuser', (string)$user);
    }
    
    public function testPassword(): void
    {
        $user = new User();
        $user->setPassword('password123');
        
        $this->assertEquals('password123', $user->getPassword());
    }
    
    public function testEmail(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        
        $this->assertEquals('test@example.com', $user->getEmail());
    }
    
    public function testRoles(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        
        $this->assertEquals(['ROLE_ADMIN'], $user->getRoles());
    }
    
    /**
     * @throws Exception
     */
    public function testAddTasks(): void
    {
        $user = new User();
        $this->assertInstanceOf(ArrayCollection::class, $user->getTasks());
        
        // Création du mock pour Task
        $taskMock = $this->createMock(Task::class);
        
        // Simule que `getUser` renvoie $user
        $taskMock->method('getUser')->willReturn($user);
        
        // Vérifie que `setUser($user)` est appelé une fois lors de l'ajout
        $taskMock->expects($this->once())
            ->method('setUser')
            ->with($user);
        
        // Ajout de la tâche
        $user->addTask($taskMock);
        $this->assertTrue($user->getTasks()->contains($taskMock));
    }
    
    /**
     * @throws Exception
     */
    public function testRemoveTasks(): void
    {
        $user = new User();
        $this->assertInstanceOf(ArrayCollection::class, $user->getTasks());
        
        $taskMock = $this->createMock(Task::class);
        
        $taskMock->method('getUser')->willReturn($user);
        
        $user->addTask($taskMock);
        $user->removeTask($taskMock);
        $this->assertFalse($user->getTasks()->contains($taskMock));
    }
    
}
