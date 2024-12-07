<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;

final class TaskTest extends TaskTestValidation
{
    public function testCreateTask(): void
    {
        $task = new Task();
        
        // Test default values set in the constructor
        $this->assertInstanceOf(\DateTime::class, $task->getCreatedAt());
        $this->assertFalse($task->isDone());
        $this->assertSame('', $task->getTitle());
        $this->assertSame('', $task->getContent());
    }
    
    public function testSetTitle(): void
    {
        $task = new Task();
        $task->setTitle('New Task');
        
        $this->assertSame('New Task', $task->getTitle());
    }
    
    public function testSetContent(): void
    {
        $task = new Task();
        $task->setContent('This is the content of the task.');
        
        $this->assertSame('This is the content of the task.', $task->getContent());
    }
    
    public function testToggle(): void
    {
        $task = new Task();
        $task->toggle(true);
        
        $this->assertTrue($task->isDone());
        
        $task->toggle(false);
        $this->assertFalse($task->isDone());
    }
    
    public function testUserAssociation(): void
    {
        $task = new Task();
        $user = new User();
        $task->setUser($user);
        
        $this->assertSame($user, $task->getUser());
    }
    
    public function testToString(): void
    {
        $task = new Task();
        $task->setTitle('Task Title');
        
        $this->assertSame('Task Title', (string)$task);
    }
    
    public function testCreatedAt(): void
    {
        $task = new Task();
        $task->setCreatedAt(new \DateTime('2020-01-01'));
        
        $this->assertInstanceOf(\DateTime::class, $task->getCreatedAt());
        $this->assertSame('2020-01-01', $task->getCreatedAt()->format('Y-m-d'));
    }
}
