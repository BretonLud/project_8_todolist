<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;

final class TaskTest extends TaskTestValidation
{
    public function testCreateTask()
    {
        $task = new Task();
        
        // Test default values set in the constructor
        $this->assertInstanceOf(\DateTime::class, $task->getCreatedAt());
        $this->assertFalse($task->isDone());
        $this->assertSame('', $task->getTitle());
        $this->assertSame('', $task->getContent());
    }
    
    public function testSetTitle()
    {
        $task = new Task();
        $task->setTitle('New Task');
        
        $this->assertSame('New Task', $task->getTitle());
    }
    
    public function testSetContent()
    {
        $task = new Task();
        $task->setContent('This is the content of the task.');
        
        $this->assertSame('This is the content of the task.', $task->getContent());
    }
    
    public function testToggle()
    {
        $task = new Task();
        $task->toggle(true);
        
        $this->assertTrue($task->isDone());
        
        $task->toggle(false);
        $this->assertFalse($task->isDone());
    }
    
    public function testUserAssociation()
    {
        $task = new Task();
        $user = new User();
        $task->setUser($user);
        
        $this->assertSame($user, $task->getUser());
    }
    
    public function testToString()
    {
        $task = new Task();
        $task->setTitle('Task Title');
        
        $this->assertSame('Task Title', (string)$task);
    }
}
