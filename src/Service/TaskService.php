<?php

namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;

readonly class TaskService
{
    public function __construct(private TaskRepository $taskRepository)
    {
    }
    
    public function getAll(): array
    {
        return $this->taskRepository->findAll();
    }
    
    public function save(Task $task): void
    {
        $this->taskRepository->save($task);
    }
    
    public function remove(Task $task): void
    {
        $this->taskRepository->remove($task);
    }
}