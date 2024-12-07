<?php

namespace App\Tests\Repository;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    private ?EntityManager $entityManager;
    
    public function testFindTasksForAdmin(): void
    {
        $taskRepository = $this->getTaskRepository();
        $manager = self::getContainer()->get('doctrine')->getManager();
        
        
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setUsername('admin');
        $admin->setPassword('<PASSWORD>');
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setUsername('user');
        $user->setPassword('<PASSWORD>');
        $manager->persist($user);
        
        $possibleUsers = [$user, null, $admin];
        
        for ($i = 0; $i < 10; $i++) {
            $task = new Task();
            $task->setTitle('Task ' . $i);
            $randomUserKey = array_rand($possibleUsers);
            $task->setUser($possibleUsers[$randomUserKey]);
            $task->setContent('Content of task ' . $i);
            $manager->persist($task);
        }
        
        $manager->flush();
        
        $accessAdmin = $admin->getRoles() === ['ROLE_ADMIN'];
        $tasksWithAdminAccess = $taskRepository->findTasksForUser($admin, $accessAdmin);
        $this->assertIsArray($tasksWithAdminAccess, 'Tasks should be found as an array with admin access.');
        
        foreach ($tasksWithAdminAccess as $task) {
            $this->assertInstanceOf(Task::class, $task, 'Task should be an instance of Task.');
        }
        
    }
    
    private function getTaskRepository(): TaskRepository
    {
        /** @var TaskRepository $taskRepository */
        $taskRepository = $this->entityManager->getRepository(Task::class);
        $this->assertInstanceOf(TaskRepository::class, $taskRepository, 'Le repository pour Task n\'est pas du bon type.');
        
        return $taskRepository;
    }
    
    public function testFindTasksForUser(): void
    {
        $taskRepository = $this->getTaskRepository();
        $manager = self::getContainer()->get('doctrine')->getManager();
        
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setUsername('user');
        $user->setPassword('<PASSWORD>');
        $manager->persist($user);
        
        for ($i = 0; $i < 5; $i++) {
            $task = new Task();
            $task->setTitle('Task ' . $i);
            $task->setUser($user);
            $task->setContent('Content of task ' . $i);
            $manager->persist($task);
        }
        
        $manager->flush();
        
        $accessAdmin = $user->getRoles() === ['ROLE_ADMIN'];
        $tasksWithoutAdminAccess = $taskRepository->findTasksForUser($user, $accessAdmin);
        $this->assertIsArray($tasksWithoutAdminAccess, 'Tasks should be found as an array without admin access.');
        
        foreach ($tasksWithoutAdminAccess as $task) {
            $this->assertInstanceOf(Task::class, $task, 'Task should be an instance of Task.');
            $this->assertEquals($user, $task->getUser(), 'Task should be owned by the user.');
        }
        
    }
    
    public function testFindTasksForUserWithNoTasks(): void
    {
        $taskRepository = $this->getTaskRepository();
        $manager = self::getContainer()->get('doctrine')->getManager();
        
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setUsername('user');
        $user->setPassword('<PASSWORD>');
        $manager->persist($user);
        $manager->flush();
        
        $accessAdmin = $user->getRoles() === ['ROLE_ADMIN'];
        $tasks = $taskRepository->findTasksForUser($user, $accessAdmin);
        $this->assertIsArray($tasks, 'Tasks should be found as an array with admin access.');
        $this->assertEmpty($tasks, 'There should be no tasks for the user.');
    }
    
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        
        if ($this->entityManager) {
            $this->entityManager->close();
        }
        
        $this->entityManager = null;
    }
}
