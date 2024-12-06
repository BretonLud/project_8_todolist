<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;

final class UserTest extends UserTestValidation
{
    public function testId()
    {
        $user = new User();
        $reflectionClass = new \ReflectionClass(User::class);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($user, 1);
        
        $this->assertEquals(1, $user->getId());
    }
    
    public function testUsername()
    {
        $user = new User();
        $user->setUsername('testuser');
        $this->assertEquals('testuser', $user->getUserIdentifier());
        $this->assertEquals('testuser', (string)$user);
    }
    
    public function testPassword()
    {
        $user = new User();
        $user->setPassword('password123');
        
        $this->assertEquals('password123', $user->getPassword());
    }
    
    public function testEmail()
    {
        $user = new User();
        $user->setEmail('test@example.com');
        
        $this->assertEquals('test@example.com', $user->getEmail());
    }
    
    public function testRoles()
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        
        $this->assertEquals(['ROLE_ADMIN'], $user->getRoles());
    }
    
    /**
     * @throws Exception
     */
    public function testTasks()
    {
        $user = new User();
        $this->assertInstanceOf(ArrayCollection::class, $user->getTasks());
        
        $taskMock = $this->createMock(Task::class);
        $taskMock->expects($this->once())
            ->method('setUser')
            ->with($user);
        
        $user->addTask($taskMock);
        
        $this->assertTrue($user->getTasks()->contains($taskMock));
        
        $user->removeTask($taskMock);
        
        $this->assertFalse($user->getTasks()->contains($taskMock));
    }
}
