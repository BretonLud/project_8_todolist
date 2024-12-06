<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class TaskController extends AbstractController
{
    public function __construct(private readonly TaskService $taskService)
    {
    }
    
    #[Route("/tasks", name: "task_list", methods: ['GET'])]
    public function listAction(): Response|RedirectResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        $accessAdmin = $this->isGranted('ROLE_ADMIN');
        
        $tasks = $this->taskService->findTasksForUser($user, $accessAdmin);
        
        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }
    
    #[Route("/tasks/create", name: "task_create", methods: ['GET', 'POST'])]
    public function createAction(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUser($this->getUser());
            $this->taskService->save($task);
            
            $this->addFlash('success', 'La tâche a été bien été ajoutée.');
            
            return $this->redirectToRoute('task_list');
        }
        
        return $this->render('task/create.html.twig', ['form' => $form]);
    }
    
    #[IsGranted('TASK_ACCESS', 'task')]
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
            'form' => $form,
            'task' => $task,
        ]);
    }
    
    #[IsGranted('TASK_ACCESS', 'task')]
    #[IsCsrfTokenValid(new Expression('"put-task-" ~ args["task"].getId()'), tokenKey: '_token')]
    #[Route("/tasks/{id}/toggle", name: "task_toggle", methods: ['PUT'])]
    public function toggleTaskAction(Task $task): RedirectResponse
    {
        $this->taskService->updateTaskStatus($task);
        
        $message = $task->isDone()
            ? sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle())
            : sprintf('La tâche %s a bien été marquée comme non faite.', $task->getTitle());
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('task_list');
    }
    
    #[IsGranted('TASK_ACCESS', 'task')]
    #[IsCsrfTokenValid(new Expression('"delete-task-" ~ args["task"].getId()'), tokenKey: '_token')]
    #[Route("/tasks/{id}/delete", name: "task_delete", methods: ['DELETE'])]
    public function deleteTaskAction(Task $task): RedirectResponse
    {
        $this->taskService->remove($task);
        
        $this->addFlash('success', 'La tâche a bien été supprimée.');
        
        return $this->redirectToRoute('task_list');
    }
}
