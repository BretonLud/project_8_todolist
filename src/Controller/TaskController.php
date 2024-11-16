<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    public function __construct(private readonly TaskService $taskService)
    {
    }
    
    #[Route("/tasks", name: "task_list", methods: ['GET'])]
    public function listAction(): Response
    {
        $tasks = $this->taskService->getAll();
        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }
    
    #[Route("/tasks/create", name: "task_create", methods: ['GET', 'POST'])]
    public function createAction(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->save($task);
            
            $this->addFlash('success', 'La tâche a été bien été ajoutée.');
            
            return $this->redirectToRoute('task_list');
        }
        
        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }
    
    #[Route("/tasks/{id}/edit", name: "task_edit", methods: ['GET', 'POST'])]
    public function editAction(Task $task, Request $request): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->save($task);
            
            $this->addFlash('success', 'La tâche a bien été modifiée.');
            
            return $this->redirectToRoute('task_list');
        }
        
        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }
    
    #[Route("/tasks/{id}/toggle", name: "task_toggle", methods: ['PUT'])]
    public function toggleTaskAction(Task $task, Request $request): RedirectResponse
    {
        $task->toggle(!$task->isDone());
        $this->taskService->save($task);
        
        if ($task->isDone()) {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));
        } else {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme non faite.', $task->getTitle()));
        }
        
        return $this->redirectToRoute('task_list');
    }
    
    #[Route("/tasks/{id}/delete", name: "task_delete", methods: ['DELETE'])]
    public function deleteTaskAction(Task $task, Request $request): RedirectResponse
    {
        $this->taskService->remove($task);
        
        $this->addFlash('success', 'La tâche a bien été supprimée.');
        
        return $this->redirectToRoute('task_list');
    }
}
