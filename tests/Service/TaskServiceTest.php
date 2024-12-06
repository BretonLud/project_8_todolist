<?php

namespace App\Tests\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskServiceTest extends TestCase
{
    private TaskRepository $taskRepository;
    private TaskService $taskService;
    
    /**
     * @throws Exception
     */
    public function testGetAll(): void
    {
        $tasks = [$this->createMock(Task::class)];
        $this->taskRepository->method('findAll')->willReturn($tasks);
        
        $result = $this->taskService->getAll();
        $this->assertSame($tasks, $result);
    }
    
    /**
     * @throws Exception
     */
    public function testRemove(): void
    {
        $task = $this->createMock(Task::class);
        $this->taskRepository->expects($this->once())
            ->method('remove')
            ->with($task);
        
        $this->taskService->remove($task);
    }
    
    /**
     * @throws Exception
     */
    public function testUpdateTaskStatus(): void
    {
        $task = $this->createMock(Task::class);
        
        $task->expects($this->once())
            ->method('toggle')
            ->with($this->isType('bool'));
        
        $this->taskRepository->expects($this->once())
            ->method('save')
            ->with($task);
        
        $this->taskService->updateTaskStatus($task);
    }
    
    /**
     * @throws Exception
     */
    public function testSave(): void
    {
        $task = $this->createMock(Task::class);
        $this->taskRepository->expects($this->once())
            ->method('save')
            ->with($task);
        
        $this->taskService->save($task);
    }
    
    /**
     * @throws Exception
     */
    public function testFindTasksForUser(): void
    {
        $user = $this->createMock(UserInterface::class);
        $accessAdmin = true;
        $tasks = [$this->createMock(Task::class)];
        
        $this->taskRepository->method('findTasksForUser')
            ->with($user, $accessAdmin)
            ->willReturn($tasks);
        
        $result = $this->taskService->findTasksForUser($user, $accessAdmin);
        $this->assertSame($tasks, $result);
    }
    
    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(TaskRepository::class);
        $this->taskService = new TaskService($this->taskRepository);
    }
}