<?php

namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class TaskService
{
    public function __construct(private TaskRepository $taskRepository)
    {
    }
    
    public function getAll(): array
    {
        return $this->taskRepository->findAll();
    }
    
    public function remove(Task $task): void
    {
        $this->taskRepository->remove($task);
    }
    
    public function updateTaskStatus(Task $task): void
    {
        $task->toggle(!$task->isDone());
        $this->save($task);
    }
    
    public function save(Task $task): void
    {
        $this->taskRepository->save($task);
    }
    
    public function findTasksForUser(UserInterface $user, bool $accessAdmin): array
    {
        return $this->taskRepository->findTasksForUser($user, $accessAdmin);
    }
    
}
